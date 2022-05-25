<?php

return [
	// Notifications - Права администратора
	[
		'type' => 'checkbox',
		'id' => 'notifications_is_admin',
		'title' => 'Группа администраторов?',
		'description' => 'Если это администраторская группа, то она получает автоматически все права и обходы',
		'level' => 'admin'
	],
	// Notifications - Права пользователя
	[
		'type' => 'checkbox',
		'id' => 'notifications_allow_view_notifications',
		'title' => 'Разрешить использование уведомлений?',
		'description' => 'Позволяет пользователю получать уведомления с сайта',
		'level' => 'user'
	],
	[
		'type' => 'checkbox',
		'id' => 'notifications_view_own_notifications_wall',
		'title' => 'Разрешить просматривать стену уведомлений?',
		'description' => 'Позволяет пользователю просматривать все свои уведомления в виде стены',
		'level' => 'user'
	],
	[
		'type' => 'checkbox',
		'id' => 'notifications_recieve_message',
		'title' => 'Получать сообщения при получении уведомления?',
		'description' => 'Позволяет пользователю получать сообщения, когда будет приходить уведомление',
		'level' => 'user'
	],
	[
		'type' => 'checkbox',
		'id' => 'notifications_recieve_mail',
		'title' => 'Получать электронное письмо при получении уведомления?',
		'description' => 'Позволяет пользователю получать сообщения в видео электронного письма, когда будет приходить уведомление',
		'level' => 'user'
	],
	[
		'type' => 'checkbox',
		'id' => 'notifications_recieve_message_unsubscribe',
		'title' => 'Разрешить отписку от сообщений на счёт уведомлений в виде ЛС?',
		'description' => 'Позволяет пользователю отключать рассылку сообщений через ЛС, когда приходит уведомление',
		'level' => 'user'
	],
	[
		'type' => 'checkbox',
		'id' => 'notifications_recieve_mail_unsubscribe',
		'title' => 'Разрешить отписку от сообщений насчёт уведомлений по почте?',
		'description' => 'Позволяет пользователю отключать рассылку сообщений по почте, когда приходит уведомление',
		'level' => 'user'
	],
	[
		'type' => 'checkbox',
		'id' => 'notifications_allow_subscribe_news',
		'title' => 'Разрешить подписываться на новости',
		'description' => 'Позволяет пользователю отключать рассылку сообщений по почте, когда приходит уведомление',
		'level' => 'user'
	],
	[
		'type' => 'checkbox',
		'id' => 'notifications_allow_mentions',
		'title' => 'Разрешить упоминать пользователей в комментариях',
		'description' => 'Если разрешено, то при упоминании пользователя при помощи знака собаки (@) упомянутый пользователь получит уведомление',
		'level' => 'user'
	],
	// Notifications - Модераторские права
	[
		'type' => 'checkbox',
		'id' => 'notifications_change_settings',
		'title' => 'Может менять настройки пользователя',
		'description' => 'Позволяет сменить настройки других пользователей',
		'level' => 'mod'
	],
];