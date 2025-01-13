<?php
//===============================================================
// Файл: MhLog.php                                              =
// Путь: engine/inc/maharder/_models/Admin/MhLog.php            =
// Дата создания: 2024-04-15 07:43:41                           =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

use Ramsey\Uuid\UuidInterface;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Uuid\Uuid1;
use Cycle\Annotated\Annotation\Table\Index;

#[Entity(role: 'mhlog', repository: MhLogRepository::class, table: 'maharder_logs')]
#[Uuid1(field: 'uuid', node: '00000fffffff', clockSeq: 0xffff, nullable: false)]
#[Index(columns: ['uuid'], unique: true)]
class MhLog extends BasisModel {
	#[Column(type: 'uuid')]
	private UuidInterface     $uuid;
	#[Column(type: 'string')]
	private string            $log_type;
	#[Column(type: 'string')]
	private string            $plugin;
	#[Column(type: 'string')]
	private string            $fn_name;
	#[Column(type: 'datetime')]
	private DateTimeImmutable $time;
	#[Column(type: 'text')]
	private string            $message;

	public function getUuid(): UuidInterface {
		return $this->uuid;
	}

	public function setUuid(UuidInterface $uuid): void {
		$this->uuid = $uuid;
	}

	public function getLogType(): string {
		return $this->log_type;
	}

	public function setLogType(string $log_type): void {
		$this->log_type = $log_type;
	}

	public function getPlugin(): string {
		return $this->plugin;
	}

	public function setPlugin(string $plugin): void {
		$this->plugin = $plugin;
	}

	public function getFnName(): string {
		return $this->fn_name;
	}

	public function setFnName(string $fn_name): void {
		$this->fn_name = $fn_name;
	}

	public function getTime(): DateTimeImmutable {
		return $this->time;
	}

	public function setTime(DateTimeImmutable $time): void {
		$this->time = $time;
	}

	public function getMessage(): string {
		return $this->message;
	}

	public function setMessage(string $message): void {
		$this->message = $message;
	}

}
