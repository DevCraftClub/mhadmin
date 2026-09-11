<?php

declare(strict_types=1);

namespace DevCraft\Dle\Schema;

use OpenApi\Attributes as OA;

/**
 * Схема таблицы `mail_campaign_users`.
 */
#[OA\Schema(schema: 'MailCampaignUsers')]
final class MailCampaignUsersSchema extends AbstractTableSchema {
	#[OA\Property(
		property: 'campaign_id',
		type: 'integer',
		description: 'ID кампании (mail_campaign_users.campaign_id)',
	)]
	public int $campaign_id = 0;
	#[OA\Property(
		property: 'user_id',
		type: 'integer',
		description: 'ID пользователя (mail_campaign_users.user_id)',
	)]
	public int $user_id = 0;

	public function table(): string {
		return 'mail_campaign_users';
	}

	protected function columnList(): array {
		return [
			'campaign_id',
			'user_id',
		];
	}

	protected function defaultMap(): array {
		return [
			'campaign_id' => 0,
			'user_id'     => 0,
		];
	}

	public function primaryKey(): string|array {
		return ['campaign_id', 'user_id'];
	}
}
