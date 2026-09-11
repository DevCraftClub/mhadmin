<?php

declare(strict_types=1);

namespace DevCraft\Dle\Schema;

use OpenApi\Attributes as OA;

/**
 * Схема таблицы `mail_campaigns`.
 */
#[OA\Schema(schema: 'MailCampaigns')]
final class MailCampaignsSchema extends AbstractTableSchema {
	#[OA\Property(
		property: 'id',
		type: 'integer',
		description: 'Первичный ключ (mail_campaigns.id)',
	)]
	public int $id = 0;
	#[OA\Property(
		property: 'type',
		type: 'integer',
		description: 'Колонка mail_campaigns.type',
	)]
	public int $type = 1;
	#[OA\Property(
		property: 'enabled',
		type: 'integer',
		description: 'Колонка mail_campaigns.enabled',
	)]
	public int $enabled = 1;
	#[OA\Property(
		property: 'send_method',
		type: 'integer',
		description: 'Колонка mail_campaigns.send_method',
	)]
	public int $send_method = 1;
	#[OA\Property(
		property: 'sender_id',
		type: 'integer',
		description: 'ID отправителя (mail_campaigns.sender_id)',
	)]
	public int $sender_id = 0;
	#[OA\Property(
		property: 'title',
		type: 'string',
		description: 'Заголовок (mail_campaigns.title)',
	)]
	public string $title = '';
	#[OA\Property(
		property: 'message',
		type: 'string',
		description: 'Текст сообщения (mail_campaigns.message)',
	)]
	public string $message = '';
	#[OA\Property(
		property: 'users_per_pass',
		type: 'integer',
		description: 'Колонка mail_campaigns.users_per_pass',
	)]
	public int $users_per_pass = 20;
	#[OA\Property(
		property: 'send_interval',
		type: 'integer',
		description: 'Колонка mail_campaigns.send_interval',
	)]
	public int $send_interval = 3;
	#[OA\Property(
		property: 'start_date',
		type: 'integer',
		description: 'Колонка mail_campaigns.start_date',
	)]
	public int $start_date = 0;
	#[OA\Property(
		property: 'total_users',
		type: 'integer',
		description: 'Колонка mail_campaigns.total_users',
	)]
	public int $total_users = 0;
	#[OA\Property(
		property: 'user_groups',
		type: 'string',
		description: 'Колонка mail_campaigns.user_groups',
	)]
	public string $user_groups = '';
	#[OA\Property(
		property: 'reg_date_from',
		type: 'integer',
		description: 'Колонка mail_campaigns.reg_date_from',
	)]
	public int $reg_date_from = 0;
	#[OA\Property(
		property: 'reg_date_to',
		type: 'integer',
		description: 'Колонка mail_campaigns.reg_date_to',
	)]
	public int $reg_date_to = 0;
	#[OA\Property(
		property: 'last_visit_from',
		type: 'integer',
		description: 'Колонка mail_campaigns.last_visit_from',
	)]
	public int $last_visit_from = 0;
	#[OA\Property(
		property: 'last_visit_to',
		type: 'integer',
		description: 'Колонка mail_campaigns.last_visit_to',
	)]
	public int $last_visit_to = 0;
	#[OA\Property(
		property: 'allow_mail',
		type: 'integer',
		description: 'Колонка mail_campaigns.allow_mail',
	)]
	public int $allow_mail = 1;

	public function table(): string {
		return 'mail_campaigns';
	}

	protected function columnList(): array {
		return [
			'id',
			'type',
			'enabled',
			'send_method',
			'sender_id',
			'title',
			'message',
			'users_per_pass',
			'send_interval',
			'start_date',
			'total_users',
			'user_groups',
			'reg_date_from',
			'reg_date_to',
			'last_visit_from',
			'last_visit_to',
			'allow_mail',
		];
	}

	protected function defaultMap(): array {
		return [
			'type'            => 1,
			'enabled'         => 1,
			'send_method'     => 1,
			'sender_id'       => 0,
			'title'           => '',
			'message'         => '',
			'users_per_pass'  => 20,
			'send_interval'   => 3,
			'start_date'      => 0,
			'total_users'     => 0,
			'user_groups'     => '',
			'reg_date_from'   => 0,
			'reg_date_to'     => 0,
			'last_visit_from' => 0,
			'last_visit_to'   => 0,
			'allow_mail'      => 1,
		];
	}

	public function primaryKey(): string|array {
		return 'id';
	}
}
