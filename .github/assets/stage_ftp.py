#!/usr/bin/env python3
"""Стадирует файлы из .github/assets/sync-map.yml в каталог для lftp mirror.

Без внешних зависимостей: поддерживается только формат карты из плана
(entries: список {repo, ftp}).
"""

from __future__ import annotations

import os
import re
import shutil
import sys
from pathlib import Path


def load_entries(map_path: Path) -> list[dict[str, str]]:
	entries: list[dict[str, str]] = []
	current: dict[str, str] | None = None
	for raw in map_path.read_text(encoding="utf-8").splitlines():
		line = raw.split("#", 1)[0].rstrip()
		if not line.strip() or line.strip() == "entries:":
			continue
		m_repo = re.match(r"^\s*-\s*repo:\s*(.+)$", line)
		if m_repo:
			if current is not None:
				entries.append(current)
			current = {"repo": m_repo.group(1).strip()}
			continue
		m_ftp = re.match(r"^\s+ftp:\s*(.+)$", line)
		if m_ftp:
			if current is None:
				sys.stderr.write(f"ftp without repo: {line}\n")
				sys.exit(1)
			current["ftp"] = m_ftp.group(1).strip()
			continue
	if current is not None:
		entries.append(current)
	if not entries:
		sys.stderr.write(f"{map_path}: no entries parsed\n")
		sys.exit(1)
	return entries


def main() -> None:
	root = Path.cwd()
	map_path = root / ".github" / "assets" / "sync-map.yml"
	stage = Path(os.environ.get("STAGE", "_ftp_stage"))
	if stage.exists():
		shutil.rmtree(stage)
	stage.mkdir(parents=True)

	for entry in load_entries(map_path):
		repo = entry.get("repo", "")
		ftp = entry.get("ftp", "")
		if not repo or not ftp:
			sys.stderr.write(f"entry without repo/ftp: {entry!r}\n")
			sys.exit(1)
		src = root / repo
		if not src.is_file():
			sys.stderr.write(f"missing repo file: {repo}\n")
			sys.exit(1)
		dest = stage / ftp
		dest.parent.mkdir(parents=True, exist_ok=True)
		shutil.copy2(src, dest)
		print(f"staged {repo} -> {ftp}")


if __name__ == "__main__":
	main()
