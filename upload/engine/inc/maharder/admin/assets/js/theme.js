const topMenu = '.top_menu',
	tp_menu = topMenu + ' .container',
	sideMenu = '.sidemenu',
	tm_ch = $(tp_menu).children('.firstLine');
let iw = 0,
	tm_ch_width = 0,
	tm_items_stop = 0,
	tm_width = $(topMenu).outerWidth();

$(document).ready(() => {

	shortMenu();

	$('.ui.checkbox').checkbox();
	$('.ui.dropdown').dropdown();
	$('.no.label.ui.dropdown').dropdown({useLabels: false});
	$('.ui.accordion').accordion();

	$('.menuToggler').on('click', () => {
		menuToggler();
	});

	// Настройки верхнего меню
	$(topMenu).visibility({
		type: 'fixed',
	});
	$('.overlay').visibility({
		type: 'fixed',
		offset: 80,
	});
	$(topMenu + ' .ui.dropdown').dropdown({
		on: 'hover',
	});
	if (is_mobile) {
		setTabs('#box-navi');
		changeMenus();
		$(window).on('resize', () => {
			changeMenus();
			setTabs('#box-navi');
		});
		$(sideMenu).visibility({
			type: 'fixed',
		});
	} else {
		$('#box-navi .item').tab();
	}

	$(document).on('click', '#box-navi.dropdown .item', function() {
		$.tab('change tab', $(this).data('tab'));
	});

	//$('.chosen').tokenfield();
	autosize(document.querySelectorAll('textarea'));

	$(document).find('.copy_text').each((id, text) => {
		var copyText = text;
		$(copyText).on('click', () => {
			copyText.select();
			copyText.setSelectionRange(0, 99999);
			document.execCommand('copy');
			let helper = $(copyText).parent().find('.copy_help').first();
			$(helper).show(500);
			setTimeout(() => {
				$(helper).hide(250);
			}, 2500);
		});
	});

	$(document).on('click', '.update_checker', function() {

		let data = $(this).data();
		checkUpdate(data.id, data.version);
	});

	statusUpdate();

});

function startLoading(text = '') {
	$('html, body').animate({
		scrollTop: $('#top').offset().top,
	}, {
		duration: 370,   // по умолчанию «400»
		easing: 'linear', // по умолчанию «swing»
	});

	if (text !== '') {
		$('.ui.dimmer .text').html(text);
	} else {
		$('.ui.dimmer .text').
			html(
				'Подождите, страница загружается <i class="fad fa-spinner fa-pulse"></i>');
	}
	$('.ui.dimmer').addClass('active');
}

function statusLoading(text = '') {
	if (text !== '') {
		$('.ui.dimmer .text').html(text);
	} else {
		$('.ui.dimmer .text').
			html(
				'Подождите, страница загружается <i class="fad fa-spinner fa-pulse"></i>');
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
			html +=
				'<div class="ui floating dropdown labeled icon button attached" id=\'' +
				selector.replace('#', '') +
				'\'>';
		} else if (select == 'class') {
			html +=
				'<div class="ui floating dropdown labeled icon button attached ' +
				selector.replace('.', '') + '">';
		}

		$(elements).find('.item').each(function() {
			if ($(this).hasClass('active')) {
				html += '<span class="text">' + $(this).html() +
				        '</span><div class="menu">';
			}
			temp += '<div class="item';
			if ($(this).hasClass('active')) {
				temp += ' active selected';
			}
			temp += '" data-tab=\'' + $(this).data('tab') + '\'>' +
			        $(this).html() + '</div>';
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
			html += '<div class="ui top attached tabular menu" id=\'' +
			        selector.replace('#', '') + '\'>';
		} else if (select == 'class') {
			html += '<div class="ui top attached tabular menu ' +
			        selector.replace('.', '') + '">';
		}

		$(elements).find('.item').each(function() {
			temp += '<a href=\'#\' class="item';
			if ($(this).hasClass('active')) {
				temp += ' active';
			}
			temp += '" data-tab=\'' + $(this).data('tab') + '\'>' +
			        $(this).html() + '</a>';
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
			$(selector).find('.item').each(function() {
				let temp = $(this).outerWidth();
				iw = Math.abs(iw + temp);
			});
		}
		thisWidth = $(selector).outerWidth();
	}

	$(document).find(tab).each(function() {
		$(tab + ' .item').tab();

		changeWidths(tab);
		$(window).resize(function() {
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
	$(tp_menu).find('.firstLine').each(function(i, e) {
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

	var utf8Overhead = function(str) {
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
	var readUntil = function(data, offset, stopchr) {
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
	var readChrs = function(data, offset, length) {
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
		var typeconvert = function(x) {
			return x;
		};

		if (!offset) {
			offset = 0;
		}
		dtype = (data.slice(offset, offset + 1));

		dataoffset = offset + 2;

		switch (dtype) {
			case 'i':
				typeconvert = function(x) {
					return parseInt(x, 10);
				};
				readData = readUntil(data, dataoffset, ';');
				chrs = readData[0];
				readdata = readData[1];
				dataoffset += chrs + 1;
				break;
			case 'b':
				typeconvert = function(x) {
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
				typeconvert = function(x) {
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

				readData = readChrs(data, dataoffset + 1,
					parseInt(stringlength, 10),
				);
				chrs = readData[0];
				readdata = readData[1];
				dataoffset += chrs + 2;
				if (chrs !== parseInt(stringlength, 10) && chrs !==
				    readdata.length) {
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

	var _utf8Size = function(str) {
		return ~-encodeURI(str).split(/%..|./).length;
	};

	var _getType = function(inp) {
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
			val = (Math.round(mixedValue) === mixedValue ? 'i' : 'd') + ':' +
			      mixedValue;
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
	let matches = document.cookie.match(new RegExp(
		'(?:^|; )' + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') +
		'=([^;]*)',
	));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, options = {}) {

	let date = new Date(Date.now() + 86400e3);
	date = date.toUTCString();

	options = {
		path: '/',
		expires: date,
		// при необходимости добавьте другие значения по умолчанию
		...options,
	};

	if (options.expires instanceof Date) {
		options.expires = options.expires.toUTCString();
	}

	let updatedCookie = encodeURIComponent(name) + '=' +
	                    encodeURIComponent(value);

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
		'max-age': -1,
	});
}

function checkUpdate(id, version) {
	startLoading();
	$.ajax({
		url: 'engine/ajax/controller.php?mod=maharder',
		data: {
			user_hash: dle_login_hash,
			module: 'maharder',
			file: 'master',
			method: 'check_update',
			resource_id: id
		},
		type: 'POST',
		success: function(data) {
			try {
				data = JSON.parse(data);
			} catch (e) {
			}
			hideLoading('');
			if (data.errors !== undefined) {
				$('body').toast({
					class: 'error',
					title: `Произошла ошибка!`,
					message: `Посмотрите в консоль браузера! F12 -> Консоль`,
					showProgress: 'bottom',
				});
				$.each(data.data, function(i, e) {
					console.table(e);
				});
			} else {
				if (version_compare(version, data.version, '>=')) {
					$('body').toast({
						class: 'info',
						title: `Информация`,
						message: `Обновлений нет!`,
						showProgress: 'bottom',
					});
				} else {
					$('.update_checker').addClass('red').data('tooltip', 'Доступна новая версия!').attr('data-tooltip', 'Доступна новая версия!');
					$.confirm({
						title: 'Появилась новая версия!',
						content: `
<div class="ui fluid card">
    <div class="content">
        <div class="right floated meta">${jQuery.timeago(new Date(data.last_update * 1000))}</div>
        ${data.title} <div class="ui red horizontal label">${data.version}</div>
    </div>
    <div class="content">
	    <span class="right floated">
			<i class="download icon"></i>
	        ${data.download_count} загрузок
	    </span>
	    <i class="sync alternate icon"></i>
	    ${data.update_count} обновлений
    </div>
</div>`,
						closeIcon: true,
						columnClass: 'four wide',
						offsetBottom: 40,
						useBootstrap: false,
						theme: 'material',
						draggable: true,
						backgroundDismiss: false,
						backgroundDismissAnimation: 'shake',
						buttons: {
							download: {
								text: 'Скачать',
								btnClass: 'ui blue button',
								keys: ['enter'],
								action: function() {
									window.open(data.download_link, '_blank').
										focus();
								},
							},
							opensite: {
								text: 'Открыть на сайте',
								btnClass: 'ui olive button',
								keys: ['space'],
								action: function() {
									window.open(data.site_link, '_blank').
										focus();
								},
							},
							close: {
								text: 'Отмена',
								btnClass: 'ui red button',
								keys: ['esc'],
								action: function() {
								},
							},
						},
					});
				}
			}

		},
	});
}

function statusUpdate() {
	let data = $(document).find('.update_checker').first().data(),
		update = getCookie(`update_check_${module_code}`);

	if (update === undefined) {
		checkUpdate(data.id, data.version);
		setCookie(`update_check_${module_code}`, 1);
	}
}

function tableCheckbox(name) {
	$(`#${name}`).checkbox({
		onChecked: function () {
			var $parentCheckBox = $(`#${name}`),
				$childCheckbox = $parentCheckBox.parents('table').find('tbody').first().find('.checkbox');
			$childCheckbox.checkbox('check');
			$('.massaction').removeClass('disabled');
		},
		onUnchecked: function () {
			var $parentCheckBox = $(`#${name}`),
				$childCheckbox = $parentCheckBox.parents('table').find('tbody').first().find('.checkbox');
			$childCheckbox.checkbox('uncheck');
			$('.massaction').addClass('disabled');

		}
	});

	$(`[data-checkbox="${name}"]`).checkbox({
		fireOnInit: true,
		onChange: function () {
			var $listGroup = $(this).closest(`[data-id="${name}"]`),
				$parentCheckbox = $(`#${name}`),
				$checkbox = $listGroup.find('.checkbox'),
				allChecked = true,
				allUnchecked = true
			;
			$checkbox.each(function () {
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
function version_compare (v1, v2, operator) { // eslint-disable-line camelcase
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
		dev: -6,
		alpha: -5,
		a: -5,
		beta: -4,
		b: -4,
		RC: -3,
		rc: -3,
		'#': -2,
		p: 1,
		pl: 1
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