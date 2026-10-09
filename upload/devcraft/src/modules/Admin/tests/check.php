<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

require_once dirname(__DIR__) . '/Services/LicenceTransport.php';
require_once dirname(__DIR__) . '/Services/LicenceClient.php';

use DevCraft\Modules\Admin\Services\LicenceClient;
use DevCraft\Modules\Admin\Services\LicenceTransport;

final class ArrayTransport implements LicenceTransport {

	/** @param array<string, mixed>|null $payload */
	public function __construct(private readonly ?array $payload) {
	}

	public function post(array $body): ?array {
		return $this->payload;
	}
}

$client = new LicenceClient();

$silent = $client->check('ABC', 'a.example', 'dle_notifications', false, new ArrayTransport(null));
assert($silent['hide'] === false);
assert($silent['message'] === 'Сервер не ответил');

$bad = $client->check(
	'NO',
	'a.example',
	'dle_notifications',
	false,
	new ArrayTransport(['ok' => false, 'error' => 'unknown_key', 'valid' => false, 'support_ended' => false]),
);
assert($bad['hide'] === false && $bad['message'] === 'Ключ не найден');

$ok = $client->check(
	'ABC',
	'a.example',
	'dle_notifications',
	false,
	new ArrayTransport(['ok' => true, 'error' => null, 'valid' => true, 'support_ended' => false]),
);
assert($ok['hide'] === true);
assert($ok['message'] === '');

$ended = $client->check(
	'ABC',
	'a.example',
	'dle_notifications',
	false,
	new ArrayTransport(['ok' => true, 'error' => null, 'valid' => true, 'support_ended' => true]),
);
assert($ended['hide'] === true);
assert($ended['message'] === 'Помощь по этой лицензии уже кончилась');

echo "ok\n";
