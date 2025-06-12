const topMenu  = '.top_menu',
	  tp_menu  = topMenu + ' .container',
	  sideMenu = '.sidemenu',
	  tm_ch    = $(tp_menu).children('.firstLine');
let iw            = 0,
	tm_ch_width   = 0,
	tm_items_stop = 0,
	tm_width      = $(topMenu).outerWidth();
let calendarSettings = (type = 'date') => {
	let settings = {
		formatter  : {
			datetime          : 'YYYY-MM-DD H:mm:ss',
			date              : 'YYYY-MM-DD',
			time              : 'H:mm:ss',
			cellTime          : 'H:mm',
			selectAdjacentDays: true
		},
		initialDate: new Date(),
		text       : {
			days         : [__('В'), __('П'), __('В'), __('С'), __('Ч'), __('П'), __('С')],
			dayNamesShort: [__('Вск'), __('Пнд'), __('Втр'), __('Срд'), __('Чтв'), __('Птн'), __('Сбт')],
			dayNames     : [
				__('Воскресенье'),
				__('Понедельник'),
				__('Вторник'),
				__('Среда'),
				__('Четверг'),
				__('Пятница'),
				__('Суббота')
			],
			months       : [
				__('Январь'),
				__('Февраль'),
				__('Март'),
				__('Апрель'),
				__('Май'),
				__('Июнь'),
				__('Июль'),
				__('Август'),
				__('Сентябрь'),
				__('Октябрь'),
				__('Ноябрь'),
				__('Декабрь')
			],
			monthsShort  : [
				__('Янв'),
				__('Фев'),
				__('Мар'),
				__('Апр'),
				__('Май'),
				__('Июн'),
				__('Июл'),
				__('Авг'),
				__('Сен'),
				__('Окт'),
				__('Ноя'),
				__('Дек')
			],
			today        : __('Сегодня'),
			now          : __('Сейчас'),
			am           : __('AM'),
			pm           : __('PM'),
			weekNo       : __('Неделя')
		}

	}

	if (type !== 'datetime') settings['type'] = type;

	return settings;
}

$(document).ready(() => {

	shortMenu();

	$('.ui.checkbox').checkbox();
	$('.ui.dropdown').dropdown();
	$('.no.label.ui.dropdown').dropdown({useLabels: false});
	$('.ui.accordion').accordion();
	$('[rel="time"]').calendar(calendarSettings('time'));
	$('[rel="date"]').calendar(calendarSettings());
	$('[rel="datetime"]').calendar(calendarSettings('datetime'));

	$('.menuToggler').on('click', () => {
		menuToggler();
	});

	// Настройки верхнего меню
	$(topMenu).visibility({
		type: 'fixed'
	});
	$('.overlay').visibility({
		type: 'fixed', offset: 80
	});
	$(topMenu + ' .ui.dropdown').dropdown({
		on: 'hover'
	});
	if (is_mobile) {
		setTabs('#box-navi');
		changeMenus();
		$(window).on('resize', () => {
			changeMenus();
			setTabs('#box-navi');
		});
		$(sideMenu).visibility({
			type: 'fixed'
		});
	} else {
		$('#box-navi .item').tab();
	}

	$(document).on('click', '#box-navi.dropdown .item', function () {
		$.tab('change tab', $(this).data('tab'));
	});

	//$('.chosen').tokenfield();
	autosize(document.querySelectorAll('textarea'));

	let clipboard = new ClipboardJS('.copy_text');

	clipboard.on('success', function (e) {
		$('body').toast({
			class       : 'info',
			title       : __(`Текст скопирован`),
			message     : __(`Текст "<b>${e.text}</b>" был скопирован в буффер обмена!`),
			showProgress: 'bottom'
		});
		e.clearSelection();
	});

	clipboard.on('error', function (e) {
		$('body').toast({
			class       : 'error',
			title       : __(`Проблема`),
			message     : __(`Произошла проблема при копировании текста в буффер обмена! Подробности в консоли!`),
			showProgress: 'bottom'
		});
		console.error(e)
	});

	$(document).on('click', '.update_checker', function () {

		let data = $(this).data();
		checkUpdate(data.id, data.version);
	});

	statusUpdate();
	tinyMCE.triggerSave();
	tinyMCE.baseURL = 'engine/editor/jscripts/tiny_mce';
	tinyMCE.suffix = '.min';
	jQuery("time.timeago").timeago();

});

/**
 * By DLE
 * @returns {number}
 */
function getBaseSize() {
	const BaseElement = document.querySelector("html");
	const BaseSize = parseFloat(window.getComputedStyle(BaseElement).getPropertyValue("font-size"));

	return BaseSize / 16;
}

function initTinyMce(id) {

	var mceHeight = 400 * getBaseSize();
	if (mceHeight > 600) mceHeight = 600;

	tinymce.init({
		selector      : id,
		language      : lang_iso,
		element_format: 'html',
		body_class    : '',
		skin          : 'oxide',

		width               : "100%",
		height              : mceHeight,
		deprecation_warnings: false,
		promotion           : false,
		cache_suffix        : `?v=${cache_id}`,
		license_key         : 'gpl',
		sandbox_iframes     : false,
		plugins             : "accordion fullscreen advlist autolink lists link image charmap anchor searchreplace visualblocks visualchars nonbreaking table codemirror dlebutton codesample quickbars autosave wordcount pagebreak toc",

		setup: function (editor) {
			editor.on('PreInit', function () {
				var shortEndedElements = editor.schema.getVoidElements();
				shortEndedElements['path'] = {};
				shortEndedElements['source'] = {};
				shortEndedElements['use'] = {};
			});
		},

		relative_urls        : false,
		convert_urls         : false,
		remove_script_host   : false,
		verify_html          : false,
		nonbreaking_force_tab: true,
		branding             : false,
		link_default_target  : '_blank',
		browser_spellcheck   : true,
		pagebreak_separator  : '{PAGEBREAK}',
		pagebreak_split_block: true,
		editable_class       : 'contenteditable',
		noneditable_class    : 'noncontenteditable',
		contextmenu          : 'image table lists',

		image_advtab    : true,
		image_caption   : true,
		image_dimensions: true,

		draggable_modal: true,
		menubar        : false,

		toolbar: [
			'bold italic underline strikethrough | align | outdent indent | bullist numlist | table | subscript superscript | hr charmap | searchreplace toc dletypo restoredraft | undo redo | fullscreen',
			'fontformatting forecolor backcolor pasteformat | link dleleech anchor | dleemo | dlemp dlaudio dletube | dlequote dlespoiler accordion dlehide codesample pagebreak dlepage | visualblocks removeformat | code'
		],

		mobile: {
			plugins: 'link image dlebutton codemirror',
			toolbar: 'bold italic underline alignleft aligncenter alignright link dleleech dlemp dlaudio dletube dlequote dlespoiler dlehide code'
		},

		toolbar_groups: {

			fontformatting: {
				icon   : 'change-case',
				tooltip: 'Formatting',
				items  : 'blocks styles fontfamily fontsizeinput lineheight'
			},

			align: {
				icon   : 'align-center',
				tooltip: 'Formatting',
				items  : 'alignleft aligncenter alignright alignjustify'
			},

			pasteformat: {
				icon   : 'paste',
				tooltip: 'Paste',
				items  : 'copy cut paste pastetext'
			}
		},

		block_formats: 'Tag (p)=p;Tag (div)=div;Header 1=h1;Header 2=h2;Header 3=h3;Header 4=h4;Header 5=h5;Header 6=h6;',
		style_formats: [
			{
				title  : 'Information Block',
				block  : 'div',
				wrapper: true,
				styles : {
					'color'           : '#333333',
					'border'          : 'solid 1px #00897B',
					'padding'         : '0.625rem',
					'background-color': '#E0F2F1',
					'box-shadow'      : 'rgb(0 0 0 / 24%) 0px 1px 2px'
				}
			},
			{
				title  : 'Warning Block',
				block  : 'div',
				wrapper: true,
				styles : {
					'border'          : 'solid 1px #FF9800',
					'padding'         : '0.625rem',
					'background-color': '#FFF3E0',
					'color'           : '#aa3510',
					'box-shadow'      : 'rgb(0 0 0 / 24%) 0px 1px 2px'
				}
			},
			{
				title  : 'Error Block',
				block  : 'div',
				wrapper: true,
				styles : {
					'border'          : 'solid 1px #FF5722',
					'padding'         : '0.625rem',
					'background-color': '#FBE9E7',
					'color'           : '#9c1f1f',
					'box-shadow'      : 'rgb(0 0 0 / 24%) 0px 1px 2px'
				}
			},
			{
				title  : 'Borders',
				block  : 'div',
				wrapper: true,
				styles : {'border': 'solid 1px #ccc', 'padding': '0.625rem'}
			},
			{
				title  : 'Borders top and bottom',
				block  : 'div',
				wrapper: true,
				styles : {'border-top': 'solid 1px #ccc', 'border-bottom': 'solid 1px #ccc', 'padding': '10px 0'}
			},
			{title: 'Use a shadow', block: 'div', styles: {'box-shadow': '0 5px 12px rgba(126,142,177,0.2)'}},
			{title: 'Increased letter spacing', inline: 'span', styles: {'letter-spacing': '1px'}},
			{title: 'Сapital letters', inline: 'span', styles: {'text-transform': 'uppercase'}},
			{
				title  : 'Gray background',
				block  : 'div',
				wrapper: true,
				styles : {'color': '#fff', 'background-color': '#607D8B', 'padding': '0.625rem'}
			},
			{
				title  : 'Brown background',
				block  : 'div',
				wrapper: true,
				styles : {'color': '#fff', 'background-color': '#795548', 'padding': '0.625rem'}
			},
			{
				title  : 'Blue background',
				block  : 'div',
				wrapper: true,
				styles : {'color': '#104d92', 'background-color': '#E3F2FD', 'padding': '0.625rem'}
			},
			{
				title  : 'Green background',
				block  : 'div',
				wrapper: true,
				styles : {'color': '#fff', 'background-color': '#009688', 'padding': '0.625rem'}
			}
		],

		image_class_list: [
			{title: 'None', value: ''},
			{title: 'Image Border', value: 'image-bordered'},
			{title: 'Image Shadow', value: 'image-shadows'},
			{title: 'Image Padding', value: 'image-padded'},
			{title: 'Borders Padding', value: 'image-bordered image-padded'},
			{title: 'Shadow Padding', value: 'image-shadows image-padded'}
		],

		codesample_languages: [
			{text: 'HTML/XML', value: 'markup'},
			{text: 'JavaScript', value: 'javascript'},
			{text: 'CSS', value: 'css'},
			{text: 'PHP', value: 'php'},
			{text: 'SQL', value: 'sql'},
			{text: 'Ruby', value: 'ruby'},
			{text: 'Python', value: 'python'},
			{text: 'Java', value: 'java'},
			{text: 'C', value: 'c'},
			{text: 'C#', value: 'csharp'},
			{text: 'C++', value: 'cpp'}
		],

		quickbars_insert_toolbar   : false,
		quickbars_selection_toolbar: 'bold italic underline quicklink | dlequote dlespoiler dlehide | forecolor backcolor styles blocks fontsizeinput lineheight',
		quickbars_image_toolbar    : 'alignleft aligncenter alignright | image link',

		autosave_ask_before_unload : true,
		autosave_interval          : '10s',
		autosave_prefix            : 'dle-editor-{path}{query}-{id}-',
		autosave_restore_when_empty: false,
		autosave_retention         : '10m',

		formats: {
			bold         : {inline: 'b'},
			italic       : {inline: 'i'},
			underline    : {inline: 'u', exact: true},
			strikethrough: {inline: 's', exact: true}
		},

		toc_depth: 4,

		dle_root: "",
		//		dle_upload_area : "template",
		//		dle_upload_user : "{$p_name}",
		//		dle_upload_news : "{$row['id']}",

		content_css: `engine/editor/css/content.css?v=${cache_id}`

	});
}

function startLoading(text = '') {
	$('html, body').animate({
		scrollTop: $('#top').offset().top
	}, {
		duration: 370,   // по умолчанию «400»
		easing  : 'linear' // по умолчанию «swing»
	});

	if (text !== '') {
		$('.ui.dimmer .text').html(text);
	} else {
		$('.ui.dimmer .text').html(__('Подождите, страница загружается <i class="fad fa-spinner fa-pulse"></i>'));
	}
	$('.ui.dimmer').addClass('active');

	$(document).find('.field.error, .field.success').each(function () {
		$(this).removeClass('error').removeClass('success');
	})
}

function statusLoading(text = '') {
	if (text !== '') {
		$('.ui.dimmer .text').html(text);
	} else {
		$('.ui.dimmer .text').html(__('Подождите, страница загружается <i class="fad fa-spinner fa-pulse"></i>'));
	}
}

function hideLoading() {
	$('.ui.dimmer').removeClass('active');
	statusLoading();
}

function setTabs(tab) {
	var thisWidth = $(tab).outerWidth(), nav = $(tab);

	function createDropDown(elements, selector) {
		var select = '', temp = '', html = '';
		if (selector[0] == '#') {
			select = 'id';
		} else if (selector[0] == '.') {
			select = 'class';
		}
		if (select == 'id') {
			html += '<div class="ui floating dropdown labeled icon button attached" id=\'' + selector.replace('#', '') + '\'>';
		} else if (select == 'class') {
			html += '<div class="ui floating dropdown labeled icon button attached ' + selector.replace('.', '') + '">';
		}

		$(elements).find('.item').each(function () {
			if ($(this).hasClass('active')) {
				html += '<span class="text">' + $(this).html() + '</span><div class="menu">';
			}
			temp += '<div class="item';
			if ($(this).hasClass('active')) {
				temp += ' active selected';
			}
			temp += '" data-tab=\'' + $(this).data('tab') + '\'>' + $(this).html() + '</div>';
		});
		html += temp;
		html += '</div></div>';

		return html;
	}

	function createMenu(elements, selector) {

		var select = '', temp = '', html = '';
		if (selector[0] == '#') {
			select = 'id';
		} else if (selector[0] == '.') {
			select = 'class';
		}
		if (select == 'id') {
			html += '<div class="ui top attached tabular menu" id=\'' + selector.replace('#', '') + '\'>';
		} else if (select == 'class') {
			html += '<div class="ui top attached tabular menu ' + selector.replace('.', '') + '">';
		}

		$(elements).find('.item').each(function () {
			temp += '<a href=\'#\' class="item';
			if ($(this).hasClass('active')) {
				temp += ' active';
			}
			temp += '" data-tab=\'' + $(this).data('tab') + '\'>' + $(this).html() + '</a>';
		});
		html += temp;
		html += '</div>';

		return html;
	}

	function resizeW(tabs, items, elements, selector) {
		let parent = $(selector).parent();
		if (items >= tabs) {
			$(selector).remove();
			$(parent).prepend(createDropDown(elements, selector));
			$('.dropdown').dropdown();
		} else {
			$(selector).remove();
			$(parent).prepend(createMenu(elements, selector));
			$(selector + ' .item').tab();
		}
	}

	function changeWidths(selector) {
		if (iw == 0) {
			$(selector).find('.item').each(function () {
				let temp = $(this).outerWidth();
				iw = Math.abs(iw + temp);
			});
		}
		thisWidth = $(selector).outerWidth();
	}

	$(document).find(tab).each(function () {
		$(tab + ' .item').tab();

		changeWidths(tab);
		$(window).resize(function () {
			changeWidths(tab);
		});

		resizeW(thisWidth, iw, nav, tab);

	});
}

function menuToggler(set = '') {
	let s = '.adminSidebar';

	if ($(s).sidebar('is visible') || set == 'hide') {
		$(s).sidebar('hide');
		if ($('body').hasClass('pushable')) {
			setTimeout(() => {
				$('body').removeClass('pushable');
			}, 1000);
		}
		$(s).removeClass('uncover');
	} else {
		$(s).sidebar('show');
		$('body').addClass('pushable');
	}
}

function itemWidth() {
	tm_ch_width = 0;
	tm_width = $('#content').outerWidth();
	let stop_set = false;
	$(tp_menu).find('.firstLine').each(function (i, e) {
		if (tm_width > tm_ch_width) {
			tm_items_stop = i;
		}
		let temp = $(e).outerWidth();
		tm_ch_width = Math.abs(tm_ch_width + temp);
		if (tm_width < tm_ch_width) {
			if (!stop_set) {
				tm_items_stop = (i - 1);
				stop_set = true;
			}
		}
	});
}

function shortMenu() {
	itemWidth();
	let items = [];

	$(document).find('.more_items').first().remove();

	if (tm_ch_width >= tm_width) {
		for (let i = tm_items_stop, max = tm_ch.length; i < max; i++) {
			let el = tm_ch[i];
			// items.push($(el).first().removeClass('firstLine'));
			items.push(el.outerHTML);
			$(el).remove();
		}

		$(tp_menu).append(`<div class="ui dropdown item firstLine more_items" tabindex="0">
\t\t\t\tПрочее <i class="dropdown icon"></i>
\t\t\t\t<div class="menu" tabindex="0">
\t\t\t\t\t${items.join('\n')}
\t\t\t\t</div>
\t\t\t</div>`);
		setTimeout(() => {
			$(document).find('.more_items').dropdown({on: 'hover'});
			$('.more_items').dropdown({on: 'hover'});
		}, 100);
	}
}

function changeMenus() {

	itemWidth();

	if (tm_width >= tm_ch_width) {
		$(topMenu).hide();
		setTimeout(() => {
			$(topMenu + '.constraint').hide();
		}, 100);
		$(sideMenu).show();
		$(sideMenu + '.constraint').hide();
	} else {
		$(topMenu).show();
		$(topMenu + '.constraint').hide();
		$(sideMenu).hide();
		setTimeout(() => {
			$(sideMenu + '.constraint').hide();
		}, 100);
		menuToggler('hide');
	}

}

function unserialize(data) {
	//  discuss at: https://locutus.io/php/unserialize/
	// original by: Arpad Ray (mailto:arpad@php.net)
	// improved by: Pedro Tainha (https://www.pedrotainha.com)
	// improved by: Kevin van Zonneveld (https://kvz.io)
	// improved by: Kevin van Zonneveld (https://kvz.io)
	// improved by: Chris
	// improved by: James
	// improved by: Le Torbi
	// improved by: Eli Skeggs
	// bugfixed by: dptr1988
	// bugfixed by: Kevin van Zonneveld (https://kvz.io)
	// bugfixed by: Brett Zamir (https://brett-zamir.me)
	// bugfixed by: philippsimon (https://github.com/philippsimon/)
	//  revised by: d3x
	//    input by: Brett Zamir (https://brett-zamir.me)
	//    input by: Martin (https://www.erlenwiese.de/)
	//    input by: kilops
	//    input by: Jaroslaw Czarniak
	//    input by: lovasoa (https://github.com/lovasoa/)
	// improved by: Rafał Kukawski
	//      note 1: We feel the main purpose of this function should be
	//      note 1: to ease the transport of data between php & js
	//      note 1: Aiming for PHP-compatibility, we have to translate objects
	// to arrays example 1:
	// unserialize('a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}')
	// returns 1: ['Kevin', 'van', 'Zonneveld'] example 2:
	// unserialize('a:2:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";}')
	// returns 2: {firstName: 'Kevin', midName: 'van'} example 3:
	// unserialize('a:3:{s:2:"ü";s:2:"ü";s:3:"四";s:3:"四";s:4:"𠜎";s:4:"𠜎";}')
	// returns 3: {'ü': 'ü', '四': '四', '𠜎': '𠜎'} example 4:
	// unserialize(undefined) returns 4: false example 5:
	// unserialize('O:8:"stdClass":1:{s:3:"foo";b:1;}') returns 5: { foo: true
	// }

	var utf8Overhead = function (str) {
		var s = str.length;
		for (var i = str.length - 1; i >= 0; i--) {
			var code = str.charCodeAt(i);
			if (code > 0x7f && code <= 0x7ff) {
				s++;
			} else if (code > 0x7ff && code <= 0xffff) {
				s += 2;
			}
			// trail surrogate
			if (code >= 0xDC00 && code <= 0xDFFF) {
				i--;
			}
		}
		return s - 1;
	};
	var readUntil = function (data, offset, stopchr) {
		var i = 2;
		var buf = [];
		var chr = data.slice(offset, offset + 1);

		while (chr !== stopchr) {
			if ((i + offset) > data.length) {
				throw Error('Invalid');
			}
			buf.push(chr);
			chr = data.slice(offset + (i - 1), offset + i);
			i += 1;
		}
		return [buf.length, buf.join('')];
	};
	var readChrs = function (data, offset, length) {
		var i, chr, buf;

		buf = [];
		for (i = 0; i < length; i++) {
			chr = data.slice(offset + (i - 1), offset + i);
			buf.push(chr);
			length -= utf8Overhead(chr);
		}
		return [buf.length, buf.join('')];
	};

	function _unserialize(data, offset) {
		var dtype;
		var dataoffset;
		var keyandchrs;
		var keys;
		var contig;
		var length;
		var array;
		var obj;
		var readdata;
		var readData;
		var ccount;
		var stringlength;
		var i;
		var key;
		var kprops;
		var kchrs;
		var vprops;
		var vchrs;
		var value;
		var chrs = 0;
		var typeconvert = function (x) {
			return x;
		};

		if (!offset) {
			offset = 0;
		}
		dtype = (data.slice(offset, offset + 1));

		dataoffset = offset + 2;

		switch (dtype) {
			case 'i':
				typeconvert = function (x) {
					return parseInt(x, 10);
				};
				readData = readUntil(data, dataoffset, ';');
				chrs = readData[0];
				readdata = readData[1];
				dataoffset += chrs + 1;
				break;
			case 'b':
				typeconvert = function (x) {
					const value = parseInt(x, 10);

					switch (value) {
						case 0:
							return false;
						case 1:
							return true;
						default:
							throw SyntaxError('Invalid boolean value');
					}
				};
				readData = readUntil(data, dataoffset, ';');
				chrs = readData[0];
				readdata = readData[1];
				dataoffset += chrs + 1;
				break;
			case 'd':
				typeconvert = function (x) {
					return parseFloat(x);
				};
				readData = readUntil(data, dataoffset, ';');
				chrs = readData[0];
				readdata = readData[1];
				dataoffset += chrs + 1;
				break;
			case 'n':
				readdata = null;
				break;
			case 's':
				ccount = readUntil(data, dataoffset, ':');
				chrs = ccount[0];
				stringlength = ccount[1];
				dataoffset += chrs + 2;

				readData = readChrs(data, dataoffset + 1, parseInt(stringlength, 10));
				chrs = readData[0];
				readdata = readData[1];
				dataoffset += chrs + 2;
				if (chrs !== parseInt(stringlength, 10) && chrs !== readdata.length) {
					throw SyntaxError('String length mismatch');
				}
				break;
			case 'a':
				readdata = {};

				keyandchrs = readUntil(data, dataoffset, ':');
				chrs = keyandchrs[0];
				keys = keyandchrs[1];
				dataoffset += chrs + 2;

				length = parseInt(keys, 10);
				contig = true;

				for (i = 0; i < length; i++) {
					kprops = _unserialize(data, dataoffset);
					kchrs = kprops[1];
					key = kprops[2];
					dataoffset += kchrs;

					vprops = _unserialize(data, dataoffset);
					vchrs = vprops[1];
					value = vprops[2];
					dataoffset += vchrs;

					if (key !== i) {
						contig = false;
					}

					readdata[key] = value;
				}

				if (contig) {
					array = new Array(length);
					for (i = 0; i < length; i++) {
						array[i] = readdata[i];
					}
					readdata = array;
				}

				dataoffset += 1;
				break;
			case 'O': {
				// O:<class name length>:"class name":<prop count>:{<props and
				// values>}
				// O:8:"stdClass":2:{s:3:"foo";s:3:"bar";s:3:"bar";s:3:"baz";}
				readData = readUntil(data, dataoffset, ':'); // read class name
				// length
				dataoffset += readData[0] + 1;
				readData = readUntil(data, dataoffset, ':');

				if (readData[1] !== '"stdClass"') {
					throw Error('Unsupported object type: ' + readData[1]);
				}

				dataoffset += readData[0] + 1; // skip ":"
				readData = readUntil(data, dataoffset, ':');
				keys = parseInt(readData[1], 10);

				dataoffset += readData[0] + 2; // skip ":{"
				obj = {};

				for (i = 0; i < keys; i++) {
					readData = _unserialize(data, dataoffset);
					key = readData[2];
					dataoffset += readData[1];

					readData = _unserialize(data, dataoffset);
					dataoffset += readData[1];
					obj[key] = readData[2];
				}

				dataoffset += 1; // skip "}"
				readdata = obj;
				break;
			}
			default:
				throw SyntaxError('Unknown / Unhandled data type(s): ' + dtype);
		}
		return [dtype, dataoffset - offset, typeconvert(readdata)];
	}

	try {
		if (typeof data !== 'string') {
			return false;
		}

		return _unserialize(data, 0)[2];
	} catch (err) {
		console.error(err);
		return false;
	}
}

function serialize(mixedValue) {
	//  discuss at: https://locutus.io/php/serialize/
	// original by: Arpad Ray (mailto:arpad@php.net)
	// improved by: Dino
	// improved by: Le Torbi (https://www.letorbi.de/)
	// improved by: Kevin van Zonneveld (https://kvz.io/)
	// bugfixed by: Andrej Pavlovic
	// bugfixed by: Garagoth
	// bugfixed by: Russell Walker (https://www.nbill.co.uk/)
	// bugfixed by: Jamie Beck (https://www.terabit.ca/)
	// bugfixed by: Kevin van Zonneveld (https://kvz.io/)
	// bugfixed by: Ben (https://benblume.co.uk/)
	// bugfixed by: Codestar (https://codestarlive.com/)
	// bugfixed by: idjem (https://github.com/idjem)
	//    input by: DtTvB
	// (https://dt.in.th/2008-09-16.string-length-in-bytes.html) input by:
	// Martin (https://www.erlenwiese.de/) note 1: We feel the main purpose of
	// this function should be to ease note 1: the transport of data between
	// php & js note 1: Aiming for PHP-compatibility, we have to translate
	// objects to arrays example 1: serialize(['Kevin', 'van', 'Zonneveld'])
	// returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
	// example 2: serialize({firstName: 'Kevin', midName: 'van'}) returns 2:
	// 'a:2:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";}' example 3:
	// serialize( {'ü': 'ü', '四': '四', '𠜎': '𠜎'}) returns 3:
	// 'a:3:{s:2:"ü";s:2:"ü";s:3:"四";s:3:"四";s:4:"𠜎";s:4:"𠜎";}'

	var val, key, okey;
	var ktype = '';
	var vals = '';
	var count = 0;

	var _utf8Size = function (str) {
		return ~-encodeURI(str).split(/%..|./).length;
	};

	var _getType = function (inp) {
		var match;
		var key;
		var cons;
		var types;
		var type = typeof inp;

		if (type === 'object' && !inp) {
			return 'null';
		}

		if (type === 'object') {
			if (!inp.constructor) {
				return 'object';
			}
			cons = inp.constructor.toString();
			match = cons.match(/(\w+)\(/);
			if (match) {
				cons = match[1].toLowerCase();
			}
			types = ['boolean', 'number', 'string', 'array'];
			for (key in types) {
				if (cons === types[key]) {
					type = types[key];
					break;
				}
			}
		}
		return type;
	};

	var type = _getType(mixedValue);

	switch (type) {
		case 'function':
			val = '';
			break;
		case 'boolean':
			val = 'b:' + (mixedValue ? '1' : '0');
			break;
		case 'number':
			val = (Math.round(mixedValue) === mixedValue ? 'i' : 'd') + ':' + mixedValue;
			break;
		case 'string':
			val = 's:' + _utf8Size(mixedValue) + ':"' + mixedValue + '"';
			break;
		case 'array':
		case 'object':
			val = 'a';
			/*
			 if (type === 'object') {
			 var objname = mixedValue.constructor.toString().match(/(\w+)\(\)/);
			 if (objname === undefined) {
			 return;
			 }
			 objname[1] = serialize(objname[1]);
			 val = 'O' + objname[1].substring(1, objname[1].length - 1);
			 }
			 */

			for (key in mixedValue) {
				if (mixedValue.hasOwnProperty(key)) {
					ktype = _getType(mixedValue[key]);
					if (ktype === 'function') {
						continue;
					}

					okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
					vals += serialize(okey) + serialize(mixedValue[key]);
					count++;
				}
			}
			val += ':' + count + ':{' + vals + '}';
			break;
		case 'undefined':
		default:
			// Fall-through
			// if the JS object has a property which contains a null value,
			// the string cannot be unserialized by PHP
			val = 'N';
			break;
	}
	if (type !== 'object' && type !== 'array') {
		val += ';';
	}

	return val;
}

function getCookie(name) {
	let matches = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + '=([^;]*)'));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, options = {}) {

	let date = new Date(Date.now() + 86400e3);
	date = date.toUTCString();

	options = {
		path: '/', expires: date, // при необходимости добавьте другие значения по умолчанию
		...options
	};

	if (options.expires instanceof Date) {
		options.expires = options.expires.toUTCString();
	}

	let updatedCookie = encodeURIComponent(name) + '=' + encodeURIComponent(value);

	for (let optionKey in options) {
		updatedCookie += '; ' + optionKey;
		let optionValue = options[optionKey];
		if (optionValue !== true) {
			updatedCookie += '=' + optionValue;
		}
	}

	document.cookie = updatedCookie;
}

function deleteCookie(name) {
	setCookie(name, '', {
		'max-age': -1
	});
}

function checkUpdate(id, version) {
	startLoading();
	$.ajax({
		url    : 'engine/ajax/controller.php?mod=maharder',
		data   : {
			user_hash  : dle_login_hash,
			module     : 'maharder',
			file       : 'master',
			method     : 'check_update',
			resource_id: id
		}, type: 'POST', success: function (data) {
			try {
				data = JSON.parse(data);
			} catch (e) {
			}
			hideLoading('');
			if (data.errors !== undefined) {
				$('body').toast({
					class       : 'error',
					title       : __('Произошла ошибка!'),
					message     : __('Посмотрите в консоль браузера! F12 -> Консоль'),
					showProgress: 'bottom'
				});
				$.each(data.data, function (i, e) {
					console.table(e);
				});
			} else {
				if (version_compare(version, data.version, '>=')) {
					$('body').toast({
						class       : 'info',
						title       : __('Информация'),
						message     : __('Обновлений нет!'),
						showProgress: 'bottom'
					});
				} else {
					$('.update_checker')
						.addClass('red')
						.data('tooltip', __('Доступна новая версия!'))
						.attr('data-tooltip', __('Доступна новая версия!'));
					$.confirm({
						title                     : __('Появилась новая версия!'),
						content                   : `
<div class="ui fluid card">
    <div class="content">
        <div class="right floated meta">${jQuery.timeago(new Date(data.last_update * 1000))}</div>
        ${data.title} <div class="ui red horizontal label">${data.version}</div>
    </div>
    <div class="content">
	    <span class="right floated">
			<i class="download icon"></i>
	        ${data.download_count} ${__('загрузок')}
	    </span>
	    <i class="sync alternate icon"></i>
	    ${data.update_count} ${__('обновлений')}
    </div>
</div>`,
						closeIcon                 : true,
						columnClass               : 'four wide',
						offsetBottom              : 40,
						useBootstrap              : false,
						theme                     : 'material',
						draggable                 : true,
						backgroundDismiss         : false,
						backgroundDismissAnimation: 'shake',
						buttons                   : {
							download   : {
								text: 'Скачать', btnClass: 'ui blue button', keys: ['enter'], action: function () {
									window.open(data.download_link, '_blank').focus();
								}
							}, opensite: {
								text    : __('Открыть на сайте'),
								btnClass: 'ui olive button',
								keys    : ['space'],
								action  : function () {
									window.open(data.site_link, '_blank').focus();
								}
							}, close   : {
								text    : __('Отмена'),
								btnClass: 'ui red button',
								keys    : ['esc'],
								action  : function () {
								}
							}
						}
					});
				}
			}

		}
	});
}

function statusUpdate() {
	let data   = $(document).find('.update_checker').first().data(),
		update = getCookie(`update_check_${module_code}`);

	if (update === undefined) {
		checkUpdate(data.id, data.version);
		setCookie(`update_check_${module_code}`, 1);
	}
}

function tableCheckbox(name) {
	$(`#${name}`).checkbox({
		onChecked     : function () {
			var $parentCheckBox = $(`#${name}`),
				$childCheckbox  = $parentCheckBox.parents('table').find('tbody').first().find('.checkbox');
			$childCheckbox.checkbox('check');
			$('.massaction').removeClass('disabled');
		}, onUnchecked: function () {
			var $parentCheckBox = $(`#${name}`),
				$childCheckbox  = $parentCheckBox.parents('table').find('tbody').first().find('.checkbox');
			$childCheckbox.checkbox('uncheck');
			$('.massaction').addClass('disabled');

		}
	});

	$(`[data-checkbox="${name}"]`).checkbox({
		fireOnInit: true, onChange: function () {
			var $listGroup      = $(this).closest(`[data-id="${name}"]`),
				$parentCheckbox = $(`#${name}`),
				$checkbox       = $listGroup.find('.checkbox'),
				allChecked      = true,
				allUnchecked    = true;
			$

			checkbox.each(function () {
				if ($(this).checkbox('is checked')) {
					allUnchecked = false;
				} else {
					allChecked = false;
				}
			});
			if (allChecked) {
				$parentCheckbox.checkbox('set checked');
				$('.massaction').removeClass('disabled');
			} else if (allUnchecked) {
				$parentCheckbox.checkbox('set unchecked');
				$('.massaction').addClass('disabled');
			} else {
				$parentCheckbox.checkbox('set indeterminate');
				$('.massaction').removeClass('disabled');
			}
		}
	});
}

/**
 *
 * @param v1
 * @param v2
 * @param operator
 * @url https://locutus.io/php/info/version_compare/
 * @returns {null|boolean|number}
 */
function version_compare(v1, v2, operator) { // eslint-disable-line camelcase
	//       discuss at: https://locutus.io/php/version_compare/
	//      original by: Philippe Jausions (https://pear.php.net/user/jausions)
	//      original by: Aidan Lister (https://aidanlister.com/)
	// reimplemented by: Kankrelune (https://www.webfaktory.info/)
	//      improved by: Brett Zamir (https://brett-zamir.me)
	//      improved by: Scott Baker
	//      improved by: Theriault (https://github.com/Theriault)
	//        example 1: version_compare('8.2.5rc', '8.2.5a')
	//        returns 1: 1
	//        example 2: version_compare('8.2.50', '8.2.52', '<')
	//        returns 2: true
	//        example 3: version_compare('5.3.0-dev', '5.3.0')
	//        returns 3: -1
	//        example 4: version_compare('4.1.0.52','4.01.0.51')
	//        returns 4: 1
	// Important: compare must be initialized at 0.
	let i
	let x
	let compare = 0
	// vm maps textual PHP versions to negatives so they're less than 0.
	// PHP currently defines these as CASE-SENSITIVE. It is important to
	// leave these as negatives so that they can come before numerical versions
	// and as if no letters were there to begin with.
	// (1alpha is < 1 and < 1.1 but > 1dev1)
	// If a non-numerical value can't be mapped to this table, it receives
	// -7 as its value.
	const vm = {
		dev: -6, alpha: -5, a: -5, beta: -4, b: -4, RC: -3, rc: -3, '#': -2, p: 1, pl: 1
	}
	// This function will be called to prepare each version argument.
	// It replaces every _, -, and + with a dot.
	// It surrounds any nonsequence of numbers/dots with dots.
	// It replaces sequences of dots with a single dot.
	//    version_compare('4..0', '4.0') === 0
	// Important: A string of 0 length needs to be converted into a value
	// even less than an unexisting value in vm (-7), hence [-8].
	// It's also important to not strip spaces because of this.
	//   version_compare('', ' ') === 1
	const _prepVersion = function (v) {
		v = ('' + v).replace(/[_\-+]/g, '.')
		v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.')
		return (!v.length ? [-8] : v.split('.'))
	}
	// This converts a version component to a number.
	// Empty component becomes 0.
	// Non-numerical component becomes a negative number.
	// Numerical component becomes itself as an integer.
	const _numVersion = function (v) {
		return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10))
	}
	v1 = _prepVersion(v1)
	v2 = _prepVersion(v2)
	x = Math.max(v1.length, v2.length)
	for (i = 0; i < x; i++) {
		if (v1[i] === v2[i]) {
			continue
		}
		v1[i] = _numVersion(v1[i])
		v2[i] = _numVersion(v2[i])
		if (v1[i] < v2[i]) {
			compare = -1
			break
		} else if (v1[i] > v2[i]) {
			compare = 1
			break
		}
	}
	if (!operator) {
		return compare
	}
	// Important: operator is CASE-SENSITIVE.
	// "No operator" seems to be treated as "<."
	// Any other values seem to make the function return null.
	switch (operator) {
		case '>':
		case 'gt':
			return (compare > 0)
		case '>=':
		case 'ge':
			return (compare >= 0)
		case '<=':
		case 'le':
			return (compare <= 0)
		case '===':
		case '=':
		case 'eq':
			return (compare === 0)
		case '<>':
		case '!==':
		case 'ne':
			return (compare !== 0)
		case '':
		case '<':
		case 'lt':
			return (compare < 0)
		default:
			return null
	}
}

/**
 * Валидирует и подсвечивает поля формы на основе переданных данных.
 *
 * @param {string[]} fields Массив имен атрибутов `name` полей, которые нужно подсветить.
 * @param {string} [status="error"] Статус валидации. Допустимые значения: "error" или "success". Определяет, какой класс добавить.
 * @param {string} [context=".form"] Селектор контекста формы. По умолчанию: `.form`.
 */
function validateForm(fields, status = 'error', context = '.form') {
	const className = status === 'error' ? 'error' : 'success'; // Определяем итоговый класс один раз
	const formFields = $(document).find(context).first(); // Кэшируем выбор документа

	fields.forEach((field) => {
		const fieldElement = formFields.find(`[name="${field}"]`).first(); // Находим один раз
		fieldElement.parent('.field').addClass(className); // Добавляем нужный класс
	});
}

/**
 * Управляет состоянием группы чекбоксов на основе состояния родительского чекбокса
 * и синхронизирует родительский чекбокс в зависимости от состояния группы дочерних чекбоксов.
 *
 * @param {string} master Имя родительского чекбокса.
 * @param {string} children Имя группы дочерних чекбоксов.
 *
 */
function toggleCheckboxGroup(master, children) {
	$(`input[name="${master}"]`).on('change', function () {
		const isChecked = $(this).prop('checked'); // Получаем состояние основного чекбокса
		$(`input[name="${children}[]"]`).each(function () {
			$(this).prop('checked', isChecked); // Устанавливаем состояние каждого чекбокса
		});
	});

	$(`input[name="${children}[]"]`).on('change', function () {
		const allChecked = $(`input[name="${children}[]"]`).length === $(`input[name="${children}[]"]:checked`).length;
		$(`input[name="${master}"]`).prop('checked', allChecked); // Если все дети выбраны, устанавливаем основной чекбокс в true, иначе - false
	});
}

/**
 * Формирует и отправляет заранее подготовленный AJAX-запрос с заданными параметрами.
 *
 * @param {Object} options Объект с параметрами запроса.
 * @param {string} options.method Метод обработки на сервере.
 * @param {Object} options.inputData Данные, отправляемые на сервер.
 * @param {string} [options.requestFile='master'] Название файла, к которому обращается серверный обработчик.
 * @param {string} [options.requestMethod='POST'] HTTP-метод запроса (например, 'POST', 'GET').
 * @param {Function} [options.successCallback] Функция, вызываемая при успешном выполнении запроса.
 * @param {Function} [options.errorCallback] Функция, вызываемая при ошибке выполнения запроса.
 * @param {string} [options.toastTitle] Заголовок уведомления (toast), отображаемого после успешного запроса.
 * @param {string} [options.toastMessage] Сообщение уведомления (toast), отображаемого после успешного запроса.
 *
 * @global {string} dle_login_hash Хэш-идентификатор текущей сессии пользователя.
 * @global {string} module_code Код модуля, отвечающего за обработку запроса.
 *
 * @see sendAjaxRequest
 */
function sendPreparedAjaxRequest({method, inputData, requestFile = 'master', requestMethod = 'POST', successCallback, errorCallback, toastTitle, toastMessage}) {
	let url = 'engine/ajax/controller.php?mod=maharder',
		data = {
			user_hash: dle_login_hash,
			module: module_code,
			file: requestFile,
			method: method,
			data: inputData
		}
	sendAjaxRequest({
		url,
		data,
		requestMethod,
		successCallback,
		errorCallback,
		toastTitle,
		toastMessage
	})
}

/**
 * Выполняет AJAX-запрос и обрабатывает результаты, включая показ уведомлений
 * и вызов переданных функций-обработчиков.
 *
 * @param {Object} params Объект с параметрами запроса.
 * @param {string} params.url URL, на который будет отправлен запрос.
 * @param {Object} [params.data] Объект данных для отправки на сервер.
 * @param {string} [params.method='POST'] Метод HTTP-запроса (например, 'GET', 'POST').
 * @param {Function} [params.successCallback] Функция, вызываемая при успешной обработке ответа.
 * @param {Function} [params.errorCallback] Функция, вызываемая при возникновении ошибки.
 * @param {string} [params.toastTitle] Заголовок уведомления (toast) при успешном выполнении запроса.
 * @param {string} [params.toastMessage] Текст уведомления (toast) при успешном выполнении запроса.
 *
 * @see startLoading
 * @see hideLoading
 */
function sendAjaxRequest({url, data, method = 'POST', successCallback, errorCallback, toastTitle, toastMessage}) {
	// Начать загрузку
	startLoading();

	// Выполнение AJAX-запроса
	$.ajax({
		url,
		data,
		type   : method,
		success: handleSuccess,
		error  : handleError
	});

	function handleSuccess(response) {
		hideLoading();

		const {message, data: responseData, redirect} = response;

		// Показ уведомления об успешной операции
		showToast({
			type   : 'success',
			title  : toastTitle || message,
			message: toastMessage || responseData?.join('<br>')
		});

		// Если передан successCallback, вызов его
		successCallback?.(response);

		// Перенаправление на новый URL через 2 секунды (если указано)
		if (redirect) {
			setTimeout(() => {
				window.location.replace(redirect);
			}, 2000);
		}
	}

	function handleError(error) {
		hideLoading();

		let parsedError = error;
		try {
			parsedError = JSON.parse(error.responseText);
		} catch (e) {
			// Оставляем исходную ошибку, если парсинг не удался
		}

		const {message, data: errorData} = parsedError;

		// Если передан errorCallback, вызов его
		errorCallback?.(parsedError);

		// Показ уведомления об ошибке
		showToast({
			type   : 'error',
			title  : message,
			message: errorData?.join('<br>')
		});
	}

	function showToast({type, title, message}) {
		$('body').toast({
			class       : type,
			title,
			message,
			showProgress: 'bottom'
		});
	}
}
