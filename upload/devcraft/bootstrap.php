<?php

declare(strict_types=1);

if(!defined('DATALIFEENGINE')) {
	exit('Hacking attempt!');
}

$baseUrl = rtrim((string) (($config['http_home_url'] ?? '/') ?: '/'), '/');
$ajaxUrl = $baseUrl . '/devcraft/bootstrap.ajax.php';
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DevCraft Bootstrap</title>
    <link rel="stylesheet" href="https://necolas.github.io/normalize.css/8.0.1/normalize.css">
    <style>
        .dc-bootstrap-log-wrap { position: relative; }
        .dc-bootstrap-log-wrap.is-loading::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.06), transparent);
            animation: dc-bootstrap-shimmer 1.4s infinite;
            pointer-events: none;
        }
        @keyframes dc-bootstrap-shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</head>
<body style="font-family:Arial,sans-serif;background:#f3f6fa;padding:24px;">
<div style="max-width:780px;margin:0 auto;background:#fff;border:1px solid #dfe6ef;border-radius:8px;padding:20px;">
    <h2 style="margin-top:0;">Инициализация DevCraft</h2>
    <p id="dc-bootstrap-status">Подготовка окружения...</p>
    <div class="dc-bootstrap-log-wrap is-loading" id="dc-bootstrap-log-wrap">
        <pre id="dc-bootstrap-log" style="white-space:pre-wrap;background:#0f172a;color:#d1fae5;padding:12px;border-radius:6px;min-height:180px;max-height:420px;overflow:auto;margin:0;"></pre>
    </div>
    <div style="margin-top:12px;">
        <button id="dc-bootstrap-retry" style="display:none;">Повторить</button>
        <a id="dc-bootstrap-dashboard" href="?mod=devcraft" style="display:none;">Перейти в DevCraft Admin</a>
    </div>
</div>
<script>
  (function () {
    var status = document.getElementById('dc-bootstrap-status');
    var log = document.getElementById('dc-bootstrap-log');
    var logWrap = document.getElementById('dc-bootstrap-log-wrap');
    var retry = document.getElementById('dc-bootstrap-retry');
    var dashboard = document.getElementById('dc-bootstrap-dashboard');
    var pollTimer = null;
    var pollDelayMs = 2000;
    var requestInFlight = false;

    function setLoading(active) {
      if (active) {
        logWrap.classList.add('is-loading');
      } else {
        logWrap.classList.remove('is-loading');
      }
    }

    function appendLine(text) {
      if (!text) {
        return;
      }
      log.textContent += (log.textContent ? "\n" : "") + text;
      log.scrollTop = log.scrollHeight;
    }

    function renderPayload(payload, op) {
      status.textContent = payload.message || '...';

      if (payload.logExcerpt) {
        log.textContent = payload.logExcerpt;
        log.scrollTop = log.scrollHeight;
      } else if (op === 'start' || op === 'retry') {
        appendLine('[' + (payload.currentStep || '-') + '] ' + (payload.message || ''));
      }

      if (payload.status === 'in_progress') {
        setLoading(true);
        return;
      }

      setLoading(false);
      clearTimeout(pollTimer);

      if (payload.status === 'failed') {
        retry.style.display = 'inline-block';
        return;
      }

      if (payload.status === 'completed') {
        dashboard.style.display = 'inline-block';
      }
    }

    function parseResponse(response) {
      var contentType = response.headers.get('content-type') || '';

      if (contentType.indexOf('json') === -1) {
        return response.text().then(function (text) {
          throw new Error(text || 'Сервер вернул не-JSON ответ');
        });
      }

      return response.json();
    }

    function request(op) {
      if (requestInFlight && op === 'status') {
        return;
      }

      requestInFlight = true;

      fetch('<?= htmlspecialchars($ajaxUrl, ENT_QUOTES); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ operation: op }).toString()
      })
        .then(parseResponse)
        .then(function (payload) {
          requestInFlight = false;
          renderPayload(payload, op);

          if (payload.status === 'in_progress') {
            clearTimeout(pollTimer);
            pollTimer = setTimeout(function () {
              request('status');
            }, pollDelayMs);
          }
        })
        .catch(function (e) {
          requestInFlight = false;
          setLoading(false);
          clearTimeout(pollTimer);
          status.textContent = 'Ошибка bootstrap';
          appendLine(String(e && e.message ? e.message : e));
          retry.style.display = 'inline-block';
        });
    }

    retry.addEventListener('click', function () {
      retry.style.display = 'none';
      dashboard.style.display = 'none';
      log.textContent = '';
      request('retry');
    });

    request('start');
  })();
</script>
</body>
</html>
