if (window.$ushb === undefined) window.$ushb = {};
$ushb.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

!function($){
	if (window.$ushb.mixins === undefined) window.$ushb.mixins = {};

	// TODO: replace AJAX URL;
	$ushb.ajaxUrl = $('.us-hb').data('ajaxurl');

	/**
	 * $ushb.Tabs class
	 *
	 * Boundable events: beforeShow, afterShow, beforeHide, afterHide
	 *
	 * @param container
	 * @constructor
	 */
	$ushb.Tabs = function(container){
		this.$container = $(container);
		this.$list = this.$container.find('.usof-tabs-list:first');
		this.$items = this.$list.children('.usof-tabs-item');
		this.$sections = this.$container.find('.usof-tabs-section');
		this.items = this.$items.toArray().map($);
		this.sections = this.$sections.toArray().map($);
		this.active = 0;
		this.items.forEach(function($elm, index){
			$elm.on('click', this.open.bind(this, index));
		}.bind(this));
	};
	$.extend($ushb.Tabs.prototype, $usof.mixins.Events, {
		open: function(index){
			if (index == this.active || this.sections[index] == undefined) return;
			if (this.sections[this.active] !== undefined) {
				this.trigger('beforeHide', this.active, this.sections[this.active], this.items[this.active]);
				this.sections[this.active].hide();
				this.items[this.active].removeClass('active');
				this.trigger('afterHide', this.active, this.sections[this.active], this.items[this.active]);
			}
			this.trigger('beforeShow', index, this.sections[index], this.items[index]);
			this.sections[index].show();
			this.items[index].addClass('active');
			this.trigger('afterShow', index, this.sections[index], this.items[index]);
			this.active = index;
		}
	});

	/**
	 * $ushb.EForm class
	 * @param container
	 * @constructor
	 */
	$ushb.EForm = function(container){
		this.$container = $(container);
		this.$tabs = this.$container.find('.usof-tabs');
		if (this.$tabs.length) {
			this.tabs = new $ushb.Tabs(this.$tabs);
		}

		this.initFields(this.$container);
	};
	$.extend($ushb.EForm.prototype, $usof.mixins.Fieldset);

	/**
	 * $ushb.Elist class: A popup with elements list to choose from. Behaves as a singleton.
	 * Boundable events: beforeShow, afterShow, beforeHide, afterHide, select
	 * @constructor
	 */
	$ushb.EList = function(){
		if ($ushb.elist !== undefined) return $ushb.elist;
		this.$container = $('.us-hb-window.for_adding');
		if (this.$container.length > 0) {
			this.$container.appendTo($(document.body));
			this.init();
		}
	};
	$.extend($ushb.EList.prototype, $usof.mixins.Events, {
		init: function(){
			this.$closer = this.$container.find('.us-hb-window-closer');
			this.$list = this.$container.find('.us-hb-window-list');
			this._events = {
				select: function(event){
					var $item = $(event.target).closest('.us-hb-window-item');
					this.hide();
					this.trigger('select', $item.data('name'));
				}.bind(this),
				hide: this.hide.bind(this)
			};
			this.$closer.on('click', this._events.hide);
			this.$list.on('click', '.us-hb-window-item', this._events.select);
		},
		show: function(){
			if (this.$container.length == 0) {
				// Loading elements list html via ajax
				$.ajax({
					type: 'post',
					url: $ushb.ajaxUrl,
					data: {
						action: 'ushb_get_elist_html'
					},
					success: function(html){
						this.$container = $(html).css('display', 'none').appendTo($(document.body));
						this.init();
						this.show();
					}.bind(this)
				});
				return;
			}

			this.trigger('beforeShow');
			this.$container.css('display', 'block');
			this.trigger('afterShow');
		},
		hide: function(){
			this.trigger('beforeHide');
			this.$container.css('display', 'none');
			this.trigger('afterHide');
		}
	});
	// Singleton instance
	$ushb.elist = new $ushb.EList;

	/**
	 * $ushb.EBuilder class: A popup with loadable elements forms
	 * Boundable events: beforeShow, afterShow, beforeHide, afterHide, save
	 * @constructor
	 */
	$ushb.EBuilder = function(){
		this.$container = $('.us-hb-window.for_editing');
		this.loaded = false;
		if (this.$container.length != 0) {
			this.$container.appendTo($(document.body));
			this.init();
		}
	};
	$.extend($ushb.EBuilder.prototype, $usof.mixins.Events, {
		init: function(){
			this.$title = this.$container.find('.us-hb-window-title');
			this.titles = this.$title[0].onclick() || {};
			this.$title.removeAttr('onclick');
			this.$closer = this.$container.find('.us-hb-window-closer, .us-hb-window-btn.for_close');
			this.$header = this.$container.find('.us-hb-window-header');
			// EForm containers and class instances
			this.$eforms = {};
			this.eforms = {};
			// Set of default values for each elements form
			this.defaults = {};
			this.$container.find('.usof-form').each(function(index, eform){
				var $eform = $(eform).css('display', 'none'),
					name = $eform.usMod('for');
				this.$eforms[name] = $eform;
			}.bind(this));
			this.$btnSave = this.$container.find('.us-hb-window-btn.for_save');
			// Actve element
			this.active = false;
			this._events = {
				hide: this.hide.bind(this),
				save: this.save.bind(this)
			};
			this.$closer.on('click', this._events.hide);
			this.$btnSave.on('click', this._events.save);
		},
		/**
		 * Show element form for a specified element name and initial values
		 * @param {String} name
		 * @param {Object} values
		 */
		show: function(name, values){
			if (this.$container.css('display') == 'block') {
				// If some other form is already shown, hiding it before proceeding
				this.hide();
			}
			if (!this.loaded) {
				this.$title.html(this.titles[name] || '');
				this.$container.css('display', 'block');
				// Loading ebuilder and initial form's html
				$.ajax({
					type: 'post',
					url: $ushb.ajaxUrl,
					data: {
						action: 'ushb_get_ebuilder_html'
					},
					success: function(html){
						if (html == '') return;
						// Removing additionally appended assets
						var regexp = /(\<link rel=\'stylesheet\' id=\'([^\']+)\'[^\>]+?\>)|(\<style type\=\"text\/css\"\>([^\<]*)\<\/style\>)|(\<script type=\'text\/javascript\' src=\'([^\']+)\'\><\/script\>)|(\<script type\=\'text\/javascript\'\>([^`]*?)\<\/script\>)/g;
						html = html.replace(regexp, '');
						this.$container.remove();
						this.$container = $(html).css('display', 'none').addClass('loaded').appendTo($(document.body));
						this.loaded = true;
						this.init();
						this.show(name, values);
					}.bind(this)
				});
				return;
			}

			if (this.eforms[name] === undefined) {
				// Initializing EForm on the first show
				if (this.$eforms[name] === undefined) return;
				this.eforms[name] = new $ushb.EForm(this.$eforms[name]);
				this.defaults[name] = this.eforms[name].getValues();
			}

			// Filling missing values with defaults
			values = $.extend({}, this.defaults[name], values);
			this.eforms[name].setValues(values);
			if (this.eforms[name].tabs !== undefined) {
				this.eforms[name].tabs.$list.appendTo(this.$header);
				this.eforms[name].tabs.open(0);
			}
			this.$container.toggleClass('with_tabs', this.eforms[name].tabs !== undefined);
			this.$eforms[name].css('display', 'block');
			this.$title.html(this.titles[name] || '');
			this.active = name;
			this.trigger('beforeShow');
			this.$container.css('display', 'block');
			this.trigger('afterShow');
		},
		hide: function(){
			this.trigger('beforeHide');
			this.$container.css('display', 'none');
			if (this.$eforms[this.active] !== undefined) this.$eforms[this.active].css('display', 'none');
			this.trigger('afterHide');
			if (this.eforms[this.active].tabs !== undefined) {
				this.eforms[this.active].tabs.$list.prependTo(this.eforms[this.active].$tabs);
			}
		},
		/**
		 * Get values of the active form
		 * @return {Object}
		 */
		getValues: function(){
			return (this.eforms[this.active] !== undefined) ? this.eforms[this.active].getValues() : {};
		},
		/**
		 * Get default values of the active form
		 * @return {Object}
		 */
		getDefaults: function(){
			return (this.defaults[this.active] || {});
		},
		save: function(){
			this.hide();
			this.trigger('save', this.getValues(), this.getDefaults());
		}

	});
	// Singletone instance
	$ushb.ebuilder = new $ushb.EBuilder;

	/**
	 * $ushb.ExportImport class: a popup with Export/Import dialog
	 * Boundable events: beforeShow, afterShow, beforeHide, afterHide, import
	 * @constructor
	 */
	$ushb.ExportImport = function(){
		this.$body = $(document.body);
		this.$container = $('.us-hb-window.for_export_import');
		if (this.$container.length != 0) {
			this.$container.appendTo(this.$body);
			this.init();
		}
	};
	$.extend($ushb.ExportImport.prototype, $usof.mixins.Events, {
		init: function(){
			this.$closer = this.$container.find('.us-hb-window-closer');
			this.$closeButton = this.$container.find('.us-hb-window-btn.for_close');
			this.$importButton = this.$container.find('.us-hb-window-btn.for_save');
			this.$row = this.$container.find('.usof-form-row').first();
			this.$rowState = this.$row.find('.usof-form-row-state');
			this.$textarea = this.$row.find('textarea');
			this.error = false;

			this._events = {
				import: function(event){
					var data = this.$textarea.val();
					if (data.charAt(0) == '{') {
						try {
							data = JSON.parse(data);
							if (data) {
								this.trigger('import', 'import', data);
								this.hide();
							}
						} catch (error) {
							this.error = true;
						}

					} else {
						this.error = true;
					}

					if (this.error) {
						this.$row.addClass('validate_error');
					}
				}.bind(this),
				hide: this.hide.bind(this)
			};


			this.$closer.on('click', this._events.hide);
			this.$closeButton.on('click', this._events.hide);
			this.$importButton.on('click', this._events.import);
		},
		show: function(value){
			this.$textarea.val(value);
			this.trigger('beforeShow');
			this.$container.css('display', 'block');
			this.trigger('afterShow');
		},
		hide: function(){
			this.trigger('beforeHide');
			this.$row.removeClass('validate_error');
			this.$container.css('display', 'none');
			this.trigger('afterHide');
		}
	});
	// Singletone instance
	$ushb.exportimport = new $ushb.ExportImport;

	/**
	 * $ushb.HTemplates class: a popup with header templates
	 * Boundable events: beforeShow, afterShow, beforeHide, afterHide, select
	 * @constructor
	 */
	$ushb.HTemplates = function(){
		this.$body = $(document.body);
		this.$container = $('.us-hb-window.for_htemplates');
		this.loaded = false;
		if (this.$container.length != 0) {
			this.$container.appendTo(this.$body);
			this.init();
		}
	};
	$.extend($ushb.HTemplates.prototype, $usof.mixins.Events, {
		init: function(){
			this.$closer = this.$container.find('.us-hb-window-closer');
			this.$list = this.$container.find('.us-hb-window-list');
			this._events = {
				select: function(event){
					var $item = $(event.target).closest('.us-hb-window-item');
					if ($ushb.instance.value.data && Object.keys($ushb.instance.value.data).length && !confirm($ushb.instance.translations['template_replace_confirm'])) return;
					this.hide();
					var data = $item.find('.us-hb-window-item-data')[0].onclick();
					this.trigger('select', $item.data('name'), data);
				}.bind(this),
				hide: this.hide.bind(this)
			};
			this.$closer.on('click', this._events.hide);
			this.$list.on('click', '.us-hb-window-item', this._events.select);
		},
		show: function(){
			if (!this.loaded) {
				this.$container.css('display', 'block');
				// Loading elements list html via ajax
				$.ajax({
					type: 'post',
					url: $ushb.ajaxUrl,
					data: {
						action: 'ushb_get_htemplates_html'
					},
					success: function(html){
						this.$container.remove();
						this.$container = $(html).css('display', 'none').addClass('loaded').appendTo($(document.body));
						this.loaded = true;
						this.init();
						this.show();
					}.bind(this)
				});
				return;
			}

			this.trigger('beforeShow');
			this.$container.css('display', 'block');
			this.$body.addClass('us-popup');
			this.trigger('afterShow');
		},
		hide: function(){
			this.trigger('beforeHide');
			this.$body.removeClass('us-popup');
			this.$container.css('display', 'none');
			this.trigger('afterHide');
		}
	});
	// Singletone instance
	$ushb.htemplates = new $ushb.HTemplates;

	/**
	 * Side settings
	 */
	var HBOptions = function(container){
		this.$container = $(container);
		this.$sections = this.$container.find('.us-hb-options-section');
		this.$sections.not('.active').children('.us-hb-options-section-content').slideUp();
		this.$container.find('.us-hb-options-section-title').click(function(event){
			var $parentSection = $(event.target).parent();
			if ($parentSection.hasClass('active')) return;
			var $previousActive = this.$sections.filter('.active');
			this.fireFieldEvent($previousActive, 'beforeHide');
			$previousActive.removeClass('active').children('.us-hb-options-section-content').slideUp(function(){
				this.fireFieldEvent($previousActive, 'afterHide');
			}.bind(this));
			this.fireFieldEvent($parentSection, 'beforeShow');
			$parentSection.addClass('active').children('.us-hb-options-section-content').slideDown(function(){
				this.fireFieldEvent($parentSection, 'afterShow');
			}.bind(this));
		}.bind(this));

		this.$container.find('.usof-subform-row, .usof-subform-wrapper').each(function(index, elm){
			elm.className = elm.className.replace('usof-subform-', 'usof-form-');
		});

		this.initFields(this.$container);

		var activeSection = this.$sections.filter('.active');
		this.fireFieldEvent(activeSection, 'beforeShow');
		this.fireFieldEvent(activeSection, 'afterShow');
	};
	$.extend(HBOptions.prototype, $usof.mixins.Fieldset, {
		getValue: function(id){
			if (id == 'state') return $ushb.instance.state;
			if (this.fields[id] === undefined) return undefined;
			return this.fields[id].getValue();
		}
	});

	/**
	 * USOF Field: Header Builder
	 */
	$usof.field['header_builder'] = {

		init: function(options){
			$ushb.instance = this;
			this.parentInit(options);
			this.$container = this.$row.find('.us-hb');
			this.$workspace = this.$container.find('.us-hb-workspace');
			this.$body = $(document.body);
			this.$window = $(window);
			this.$editor = $('.us-hb-editor');
			this.$dragshadow = $('<div class="us-hb-editor-dragshadow"></div>');
			this.$rows = this.$container.find('.us-hb-editor-row');
			this.$stateTabs = this.$container.find('.us-hb-state');

			this.params = this.$container.find('.us-hb-params')[0].onclick() || {};
			this.elmsDefaults = this.$container.find('.us-hb-defaults')[0].onclick() || {};
			this.translations = this.$container.find('.us-hb-translations')[0].onclick() || {};
			this.value = this.$container.find('.us-hb-value')[0].onclick() || {};
			this.states = ['default', 'tablets', 'mobiles'];
			this.state = 'default';

			this._events = {
				_maybeDragMove: this._maybeDragMove.bind(this),
				_dragMove: this._dragMove.bind(this),
				_dragEnd: this._dragEnd.bind(this)
			};

			this.$places = {hidden: this.$editor.find('.us-hb-editor-row.for_hidden > .us-hb-editor-row-h')};
			this.$editor.find('.us-hb-editor-cell').each(function(index, cell){
				var $cell = $(cell);
				this.$places[$cell.parent().parent().usMod('at') + '_' + $cell.usMod('at')] = $cell;
			}.bind(this));
			this.$wrappers = {};
			this.$editor.find('.us-hb-editor-wrapper').each(function(index, wrapper){
				var $wrapper = $(wrapper);
				this.$wrappers[$wrapper.data('id')] = $wrapper;
			}.bind(this));
			this.$elms = {};
			this.$editor.find('.us-hb-editor-elm').each(function(index, elm){
				var $elm = $(elm);
				this.$elms[$elm.data('id')] = $elm;
			}.bind(this));

			this.$templatesBtn = $('.usof-control.for_htemplates').on('click', this._showTemplatesBtnClick.bind(this));
			$('.usof-control.for_import').on('click', this._showExportImportBtnClick.bind(this));

			// Elements modification events
			this.$container.on('click', '.us-hb-editor-add, .us-hb-editor-control.type_add, .us-hb-editor-wrapper-content:empty', this._addBtnClick.bind(this));
			this.$container.on('click', '.us-hb-editor-control.type_edit', this._editBtnClick.bind(this));
			this.$container.on('click', '.us-hb-editor-control.type_clone', this._cloneBtnClick.bind(this));
			this.$container.on('mousedown', '.us-hb-editor-elm, .us-hb-editor-wrapper', this._dragStart.bind(this));
			this.$container.on('click', '.us-hb-editor-control.type_delete', this._deleteBtnClick.bind(this));

			// Preventing browser native drag event
			this.$container.on('dragstart', function(event){
				event.preventDefault();
			});

			// Options that has no responsive values
			this.sharedOptions = ['top_fullwidth', 'middle_fullwidth', 'bottom_fullwidth'];

			this.sideOptions = new HBOptions(this.$container.find('.us-hb-options:first'));
			$.each(this.sideOptions.fields, function(fieldId, field){
				field.on('change', this._optionChanged.bind(this));
			}.bind(this));
			this.sideOptions.fields.orientation.$row.find('label').on('click', function(event){
				if (!confirm(this.translations['orientation_change_confirm'])) event.preventDefault();
			}.bind(this));

			// State togglers
			this.$stateTabs.on('click', function(event){
				var $stateTab = $(event.target),
					newState = $stateTab.usMod('for');
				this.setState(newState);
			}.bind(this));

			// Highlight rows on side options hover
			this.$container.find('.us-hb-options-section').each(function(index, section){
				var $section = $(section),
					id = $section.data('id');
				$section.hover(function(){
					this.$editor.addClass('highlight_' + id);
				}.bind(this), function(){
					this.$editor.removeClass('highlight_' + id);
				}.bind(this));
			}.bind(this));

			// Showing templates for empty case
			if (!this.value.data || !Object.keys(this.value.data).length) this.$templatesBtn.addClass('start');
		},
		setValue: function(value){
			// Fixing missing datas
			if (!value) value = {};
			if (value.data === undefined) value.data = {};
			if (value.default === undefined) value.default = {};
			if (value.default.options === undefined) value.default.options = {};
			this.value = $.extend({}, value);
			this.setState('default', true);
		},
		getValue: function(){
			return this.value;
		},

		/**
		 * Buttons events
		 */
		_addBtnClick: function(event){
			var $target = $(event.target),
				placeType, place;
			if ($target.hasClass('us-hb-editor-add')) {
				var $cell = $target.closest('.us-hb-editor-cell');
				place = $cell.parent().parent().usMod('at') + '_' + $cell.usMod('at');
				placeType = 'cell';
			} else {
				place = $target.closest('.us-hb-editor-wrapper').data('id');
				placeType = place.split(':')[0];
			}
			$ushb.elist.off('beforeShow').on('beforeShow', function(){
				$ushb.elist.$container
					.toggleClass('hide_search', this.value.data['search:1'] !== undefined)
					.toggleClass('hide_cart', this.value.data['cart:1'] !== undefined)
					.usMod('orientation', this.value[this.state].options.orientation)
					.usMod('addto', placeType);
			}.bind(this));
			$ushb.elist.off('select').on('select', function(elist, type){
				var elmId = this.createElement(place, type);
				// Opening editing form for standard elements
				if (type.substr(1) != 'wrapper') {
					this.$elms[elmId].find('.us-hb-editor-control.type_edit').trigger('click');
				}
			}.bind(this));
			$ushb.elist.show();
		},
		_editBtnClick: function(event){
			var $target = $(event.target),
				$elm = $target.closest('.us-hb-editor-elm, .us-hb-editor-wrapper'),
				id = $elm.data('id'),
				type = id.split(':')[0],
				values = (this.value.data[id] || {});
			$ushb.ebuilder.off('save').on('save', function(ebuilder, values, defaults){
				this.updateElement(id, values);
			}.bind(this));
			$ushb.ebuilder.show(type, values);
		},
		_cloneBtnClick: function(event){
			var $target = $(event.target),
				$elm = $target.closest('.us-hb-editor-elm, .us-hb-editor-wrapper'),
				id = $elm.data('id'),
				type = id.split(':')[0];
			// createElement: function(place, type, index, values){
			var newId = this.createElement('top_left', type, undefined, this.value.data[id] || {});
			this.states.forEach(function(state){
				this.moveElement(newId, id, 'after', state);
			}.bind(this));
		},
		_deleteBtnClick: function(event){
			var $target = $(event.target);
			if (!confirm(this.translations['element_delete_confirm'])) return;
			var id = $target.parent().parent().data('id');
			this.deleteElement(id);
		},
		_showTemplatesBtnClick: function(event){
			if (event !== undefined) event.preventDefault();
			$ushb.htemplates.off('select').on('select', function(dialog, name, data){
				this.setValue(data);
				this.trigger('change', this.value);
			}.bind(this));
			$ushb.htemplates.show();
			this.$templatesBtn.removeClass('start');
		},
		_showExportImportBtnClick: function(event){
			event.preventDefault();
			$ushb.exportimport.off('import').on('import', function(dialog, name, data){
				this.setValue(data);
				this.trigger('change', this.value);
			}.bind(this));
			$ushb.exportimport.show(JSON.stringify(this.getValue()));
		},

		// Drag'n'drop functions
		_dragStart: function(event){
			event.stopPropagation();
			this.$draggedElm = $(event.target).closest('.us-hb-editor-elm, .us-hb-editor-wrapper');
			this.elmType = this.$draggedElm.data('id').split(':')[0];
			this.detached = false;
			this._updateBlindSpot(event);
			this.elmPointerOffset = [parseInt(event.pageX), parseInt(event.pageY)];
			this.$body.on('mousemove', this._events._maybeDragMove);
			this.$window.on('mouseup', this._events._dragEnd);
		},
		_updateBlindSpot: function(event){
			this.blindSpot = [event.pageX, event.pageY];
		},
		_isInBlindSpot: function(event){
			return Math.abs(event.pageX - this.blindSpot[0]) <= 20 && Math.abs(event.pageY - this.blindSpot[1]) <= 20;
		},
		_maybeDragMove: function(event){
			event.stopPropagation();
			if (this._isInBlindSpot(event)) return;
			this.$body.off('mousemove', this._events._maybeDragMove);
			this._detach();
			this.$body.on('mousemove', this._events._dragMove);
		},
		_dragMove: function(event){
			event.stopPropagation();
			this.$draggedElm.css({
				left: event.pageX - this.elmPointerOffset[0],
				top: event.pageY - this.elmPointerOffset[1]
			});
			if (this._isInBlindSpot(event)) return;
			var elm = event.target;
			// Checking two levels up
			for (var level = 0; level <= 2; level++, elm = elm.parentNode) {
				if (this._isShadow(elm)) return;
				// Workaround for IE9-10 that don't support css pointer-events property
				if (this._hasClass(elm, 'detached')) {
					this.$draggedElm.detach();
					break;
				}
				var parentType;
				if (this._isSortable(elm)) {
					parentType = this._isWrapperContent(elm.parentNode) ? ($(elm).parent().parent().usMod('type')[0] + 'wrapper') : 'cell';
					if (!this._canBeDropped(this.elmType, parentType)) break;
					// Dropping element before or after sortables based on their relative position in DOM
					var nextElm = elm.previousSibling,
						shadowAtLeft = false;
					while (nextElm) {
						if (nextElm == this.$dragshadow[0]) {
							shadowAtLeft = true;
							break;
						}
						nextElm = nextElm.previousSibling;
					}
					this.$dragshadow[shadowAtLeft ? 'insertAfter' : 'insertBefore'](elm);
					this._dragDrop(event);
					break;
				} else if (this._isWrapperContent(elm)) {
					if ($.contains(elm, this.$dragshadow[0])) break;
					parentType = $(elm).parent().usMod('type')[0] + 'wrapper';
					if (!this._canBeDropped(this.elmType, parentType)) break;
					// Cannot drop a wrapper to the wrapper of the same type
					this.$dragshadow.appendTo(elm);
					this._dragDrop(event);
					break;
				} else if (this._isControls(elm)) {
					if (!this._canBeDropped(this.elmType, 'cell')) break;
					// Always dropping element before controls
					this.$dragshadow.insertBefore(elm);
					this._dragDrop(event);
					break;
				} else if (this._hasClass(elm, 'us-hb-editor-cell')) {
					if (!this._canBeDropped(this.elmType, 'cell')) break;
					// If not already in this cell, moving to it
					var $shadowCell = this.$dragshadow.closest('.us-hb-editor-cell');
					if ($shadowCell.length == 0 || $shadowCell[0] != elm) {
						this.$dragshadow.insertBefore($(elm).find('.us-hb-editor-add'));
						this._dragDrop(event);
					}
					break;
				} else if (this._hasClass(elm, 'us-hb-editor-row for_hidden')) {
					// Moving to hidden elements container directly
					if (!this.$dragshadow.closest('.us-hb-editor-row').hasClass('for_hidden')) {
						this.$dragshadow.appendTo($(elm).children('.us-hb-editor-row-h'));
						this._dragDrop(event);
					}
					break;
				}
			}
		},
		_detach: function(event){
			var offset = this.$draggedElm.offset();
			this.elmPointerOffset[0] -= offset.left;
			this.elmPointerOffset[1] -= offset.top;
			this.$dragshadow.css({
				width: this.$draggedElm.outerWidth(),
				height: this.$draggedElm.outerHeight()
			}).insertBefore(this.$draggedElm);
			this.$draggedElm.addClass('detached').css({
				position: 'absolute',
				'pointer-events': 'none',
				zIndex: 10000,
				width: this.$draggedElm.width(),
				height: this.$draggedElm.height()
			}).css(offset).appendTo(this.$body);
			this.$editor.addClass('dragstarted');
			this.detached = true;
		},
		/**
		 * Complete drop
		 * @param event
		 */
		_dragDrop: function(event){
			this.$container.find('.us-hb-editor-wrapper').removeClass('empty').find('.us-hb-editor-wrapper-content:empty').parent().addClass('empty');
			this._updateBlindSpot(event);
		},
		_dragEnd: function(event){
			this.$body.off('mousemove', this._events._maybeDragMove).off('mousemove', this._events._dragMove);
			this.$window.off('mouseup', this._events._dragEnd);
			if (this.detached) {
				this.$draggedElm.removeClass('detached').removeAttr('style').insertBefore(this.$dragshadow);
				this.$dragshadow.detach();
				this.$editor.removeClass('dragstarted');
				// Getting the new element position and performing the actual drag
				var elmId = this.$draggedElm.data('id'),
					$prev = this.$draggedElm.prev();
				if ($prev.length == 0) {
					var $parent = this.$draggedElm.parent().closest('.us-hb-editor-cell, .us-hb-editor-wrapper, .us-hb-editor-row.for_hidden'),
						place = 'hidden';
					if ($parent.hasClass('us-hb-editor-cell')) {
						place = $parent.parent().parent().usMod('at') + '_' + $parent.usMod('at');
					} else if ($parent.hasClass('us-hb-editor-wrapper')) {
						place = $parent.data('id');
					}
					this.moveElement(elmId, place, 'first_child')
				} else {
					this.moveElement(elmId, $prev.data('id'), 'after');
				}
			}
		},
		_hasClass: function(elm, cls){
			return (' ' + elm.className + ' ').indexOf(' ' + cls + ' ') > -1;
		},
		_isShadow: function(elm){
			return this._hasClass(elm, 'us-hb-editor-dragshadow');
		},
		_isSortable: function(elm){
			return this._hasClass(elm, 'us-hb-editor-elm') || this._hasClass(elm, 'us-hb-editor-wrapper');
		},
		_isWrapperContent: function(elm){
			return this._hasClass(elm, 'us-hb-editor-wrapper-content');
		},
		_isControls: function(elm){
			return this._hasClass(elm, 'us-hb-editor-add');
		},
		_canBeDropped: function(elmType, placeType){
			if (elmType == 'hwrapper') {
				if (placeType == 'hwrapper') return false;
				if (placeType == 'cell' && this.value[this.state].options.orientation == 'hor') return false;
			}
			else if (elmType == 'vwrapper') {
				if (placeType == 'vwrapper') return false;
				if (placeType == 'cell' && this.value[this.state].options.orientation == 'ver') return false;
			}
			return true;
		},
		setState: function(newState, force){
			if (newState == this.state && !force) return;
			// Changing the active tab setting
			this.$stateTabs.removeClass('active').filter('.for_' + newState).addClass('active');
			this.$workspace.usMod('for', newState);
			this.state = newState;
			// Changing side options view
			if (this.value[newState].options !== undefined) {
				var options = $.extend({}, this.value[newState].options);
				if (newState != 'default') {
					for (var i = 0; i < this.sharedOptions.length; i++) {
						options[this.sharedOptions[i]] = this.value.default.options[this.sharedOptions[i]];
					}
				}
				this.setOptions(options);
			}
			this.renderLayout();
		},

		/**
		 * Create element at the end of the specified place
		 * @param {String} place Place Cell name or wrapper ID
		 * @param {String} type Element type Element type
		 * @param {Number} [index] Element index, starting from 1. If not set will be generated automatically.
		 * @param {Object} [values] Element values
		 * @returns {String} New element ID
		 * @private
		 */
		createElement: function(place, type, index, values){
			if (index === undefined) {
				// If index is not defined generating a spare one
				index = 1;
				while (this.value.data[type + ':' + index] !== undefined) index++;
			}
			var id = type + ':' + index;
			for (var i = 0, state = this.states[i]; i < this.states.length; state = this.states[++i]) {
				if (this.value[state] === undefined) this.value[state] = {};
				if (this.value[state].layout === undefined) this.value[state].layout = {};
				if (this.value[state].layout[place] === undefined) this.value[state].layout[place] = [];
				this.value[state].layout[place].push(id);
				if (type.substr(1) == 'wrapper') this.value[state].layout[id] = [];
			}
			this.value.data[id] = $.extend({}, this.elmsDefaults[type] || {}, values || {});
			this.renderLayout();
			this.trigger('change', this.value);
			return id;
		},

		/**
		 * Move a specified element to a specified place
		 * @param {String} id Element ID
		 * @param {String} place Cell name or element ID
		 * @param {String} [position] Relation to place: "last_child" / "first_child" / "before" / "after"
		 * @param {String} [state] If not specified, the current active state will be used
		 * @private
		 */
		moveElement: function(id, place, position, state){
			if (this.value.data[id] === undefined) return;
			position = position || 'last_child';
			state = state || this.state;
			if (this.value[state] === undefined) this.value[state] = {};
			if (this.value[state].layout === undefined) this.value[state].layout = {};
			// Cropping out the element from the previous place ...
			var plc, elmPos;
			for (plc in this.value[state].layout) {
				if (!this.value[state].layout.hasOwnProperty(plc)) continue;
				elmPos = this.value[state].layout[plc].indexOf(id);
				if (elmPos != -1) {
					this.value[state].layout[plc].splice(elmPos, 1);
					break;
				}
			}
			// ... and placing it to the new one
			if (position == 'first_child' || position == 'last_child') {
				if (this.value[state].layout[place] === undefined) this.value[state].layout[place] = [];
				this.value[state].layout[place][(position == 'first_child') ? 'unshift' : 'push'](id);
			} else if (position == 'before' || position == 'after') {
				for (plc in this.value[state].layout) {
					if (!this.value[state].layout.hasOwnProperty(plc)) continue;
					elmPos = this.value[state].layout[plc].indexOf(place);
					if (elmPos != -1) {
						this.value[state].layout[plc].splice(elmPos + ((position == 'after') ? 1 : 0), 0, id);
						break;
					}
				}
			}
			this.renderLayout();
			this.trigger('change', this.value);
		},

		/**
		 * Update the specified element's values
		 * @param {String} id Element ID
		 * @param {Object} values Element values
		 * @private
		 */
		updateElement: function(id, values){
			var type = id.split(':')[0];
			this.value.data[id] = $.extend({}, this.elmsDefaults[type] || {}, values);
			var $elm = this[(type.substr(1) == 'wrapper') ? '$wrappers' : '$elms'][id];
			if ($elm !== undefined) {
				this._updateElementPlaceholder($elm, id, this.value.data[id]);
			}
			this.trigger('change', this.value);
		},

		/**
		 * Delete the specified element
		 * @param {String} id Element ID
		 * @private
		 */
		deleteElement: function(id){
			var type = id.split(':')[0];
			for (var i = 0, state = this.states[i]; i < this.states.length; state = this.states[++i]) {
				if (this.value[state] === undefined) this.value[state] = {};
				if (this.value[state].layout === undefined) this.value[state].layout = {};
				if (this.value[state].layout.hidden === undefined) this.value[state].layout.hidden = [];
				if (id.substr(1, 7) == 'wrapper' && this.value[state].layout[id] !== undefined) {
					// Moving wrapper's inner elements to hidden block
					this.value[state].layout.hidden = this.value[state].layout.hidden.concat(this.value[state].layout[id]);
					delete this.value[state].layout[id];
				}
				for (var plc in this.value[state].layout) {
					if (!this.value[state].layout.hasOwnProperty(plc)) continue;
					var elmPos = this.value[state].layout[plc].indexOf(id);
					if (elmPos != -1) {
						this.value[state].layout[plc].splice(elmPos, 1);
						break;
					}
				}
			}
			if (this.value.data[id] !== undefined) delete this.value.data[id];
			this.renderLayout();
			this.trigger('change', this.value);
		},

		/**
		 * Load attachments withing the given jQuery DOM object
		 * @param {jQuery} $html
		 */
		_loadAttachments: function($html){
			$html.find('img[data-wpattachment]').each(function(index, elm){
				var $elm = $(elm),
					id = $elm.data('wpattachment'),
					attachment = wp.media.attachment(id);
				if (!attachment || !attachment.attributes.id) return '';
				var renderAttachmentImage = function(){
					var src = attachment.attributes.url;
					if (attachment.attributes.sizes !== undefined) {
						var size = (attachment.attributes.sizes.medium !== undefined) ? 'medium' : 'full';
						src = attachment.attributes.sizes[size].url;
					}
					$elm.attr('src', src).removeAttr('data-wpattachment');
				};
				if (attachment.attributes.url !== undefined) {
					renderAttachmentImage();
				} else {
					// Loading missing data via ajax
					attachment.fetch({success: renderAttachmentImage});
				}
			}.bind(this));
		},

		/**
		 * Create a base part of elements DOM placeholder: the one that doesn't depend on values
		 * @param {String} id
		 * @returns {jQuery} Created (but not placed to document) placeholder's DOM element
		 * @private
		 */
		_createElementPlaceholderBase: function(id){
			var type = id.split(':')[0],
				html = '';
			if (type.substr(1) == 'wrapper') {
				// Wrappers
				html += '<div class="us-hb-editor-wrapper type_' + ((type == 'hwrapper') ? 'horizontal' : 'vertical') + ' empty">';
				html += '<div class="us-hb-editor-wrapper-content"></div>';
				html += '<div class="us-hb-editor-wrapper-controls">';
				html += '<a title="' + this.translations['add_element'] + '" class="us-hb-editor-control type_add" href="javascript:void(0)"></a>';
				html += '<a title="' + this.translations['edit_wrapper'] + '" class="us-hb-editor-control type_edit" href="javascript:void(0)"></a>';
				html += '<a title="' + this.translations['delete_wrapper'] + '" class="us-hb-editor-control type_delete" href="javascript:void(0)"></a>';
				html += '</div>';
				html += '</div>';
				this.$wrappers[id] = $(html).data('id', id);
				return this.$wrappers[id];
			} else {
				// Standard elements
				html += '<div class="us-hb-editor-elm type_' + type + '">';
				html += '<div class="us-hb-editor-elm-content"></div>';
				html += '<div class="us-hb-editor-elm-controls">';
				html += '<a href="javascript:void(0)" class="us-hb-editor-control type_edit" title="' + this.translations['edit_element'] + '"></a>';
				html += '<a href="javascript:void(0)" class="us-hb-editor-control type_clone" title="' + this.translations['clone_element'] + '"></a>';
				html += '<a href="javascript:void(0)" class="us-hb-editor-control type_delete" title="' + this.translations['delete_element'] + '"></a>';
				html += '</div>';
				html += '</div>';
				this.$elms[id] = $(html).data('id', id);
				return this.$elms[id];
			}
		},

		/**
		 * Update element DOM placeholder with the current values
		 * @param {jQuery} $elm
		 * @param {String} id
		 * @param {Object} values
		 * @private
		 */
		_updateElementPlaceholder: function($elm, id, values){
			if (id.substr(1, 7) == 'wrapper') return;
			values = $.extend({}, this.elmsDefaults[type] || {}, values || {});
			var type = id.split(':')[0],
				$content = $elm.find('.us-hb-editor-elm-content:first'),
				content = '';
			if (type == 'text' && (values.text || values.icon)) {
				if (values.icon) {
					content += $usof.instance.prepareIconTag(values.icon);
				}
				// Strip tags
				content += values.text.replace(/(\r\n|\n|\r)+/gm, ' ').replace(/<\/?([^>]+)?>/gi, '');
			} else if (type == 'image') {
				if (values.img) {
					var imgValue = values.img;
					if (imgValue.indexOf('|') != -1) imgValue = imgValue.substr(0, imgValue.indexOf('|'));
					if ($.isNumeric(imgValue)) {
						content += '<img src="" data-wpattachment="' + imgValue + '" />';
					} else {
						content += '<img src="' + imgValue + '" />';
					}
				} else {
					content += '<i class="fa fa-image"></i>';
				}
			} else if (type == 'menu') {
				if (values.source) {
					content += this.params.navMenus[values.source] || values.source;
				} else {
					content += this.translations['menu'];
				}
			} else if (type == 'additional_menu') {
				if (values.source) {
					content += this.params.navMenus[values.source] || values.source;
				} else {
					content += this.translations['additional_menu'];
				}
			} else if (type == 'search' && values.text) {
				content += values.text.replace(/(\r\n|\n|\r)+/gm, ' ').replace(/<\/?([^>]+)?>/gi, '');
			} else if (type == 'dropdown') {
				if (values.source == 'wpml') {
					content += 'WPML';
				} else if (values.source == 'polylang') {
					content += 'Polylang';
				} else if (values.source == 'qtranslate') {
					content += 'qTranslate X';
				} else {
					content += values.link_title || this.translations['dropdown'];
				}
			} else if (type == 'socials') {
				var socialsHtml = '';
				$.each(values, function(key, value){
					if (key == 'style' || key == 'color' || key == 'hover' || key.substr(0, 7) == 'custom_' || key.substr(0, 4) == 'size' || key == 'design_options') return;
					if (value != '') socialsHtml += '<i class="fa fa-' + key + '"></i>';
				});
				if (values.custom_icon && values.custom_url) {
					socialsHtml += $usof.instance.prepareIconTag(values.custom_icon);
				}
				content += socialsHtml || this.translations['social_links'];
			} else if (type == 'btn') {
				if (values.icon) {
					content += $usof.instance.prepareIconTag(values.icon);
				}
				if (values.label) {
					content += values.label.replace(/(\r\n|\n|\r)+/gm, ' ').replace(/<\/?([^>]+)?>/gi, '');
				} else {
					content += this.translations['button'];
				}
			} else if (type == 'html') {
				content += 'HTML';
			} else if (type == 'cart') {
				if (values.icon) {
					content += $usof.instance.prepareIconTag(values.icon);
				}
				content += this.translations['cart'];
			} else {
				content += type[0].toUpperCase() + type.substr(1);
			}
			$content.html(content);
			this._loadAttachments($content);
		},

		/**
		 * Create DOM placeholder element for the specified header builder element / wrapper
		 * @param {String} id Element ID
		 * @param {Object} [values]
		 * @returns {jQuery} Created (but not yet placed to document) jQuery object with the element's DOMElement
		 * @private
		 */
		_createElementPlaceholder: function(id, values){
			var type = id.split(':')[0],
				$elm = this._createElementPlaceholderBase(id);
			this._updateElementPlaceholder($elm, id, values);
			return $elm;
		},

		/**
		 * Delete DOM placeholder for the specified header element / wrapper
		 * @param {String} id
		 * @private
		 */
		_removeElementPlaceholder: function(id){
			var container = (id.substr(1, 7) == 'wrapper') ? '$wrappers' : '$elms';
			if (this[container][id] === undefined) return;
			this[container][id].remove();
			delete this[container][id];
		},

		/**
		 * Render current layout based on current value and state
		 */
		renderLayout: function(){
			// Making sure the provided data is consistent
			if (this.value.data === undefined) this.value.data = {};
			if (this.value[this.state].layout === undefined) this.value[this.state].layout = {};
			if (this.value[this.state].layout.hidden === undefined) this.value[this.state].layout.hidden = [];
			var elmsInNextLayout = [],
				plc, i, elmId;
			for (plc in this.value[this.state].layout) {
				if (!this.value[this.state].layout.hasOwnProperty(plc)) continue;
				for (i = 0; i < this.value[this.state].layout[plc].length; i++) {
					var id = this.value[this.state].layout[plc][i],
						type = id.split(':')[0];
					if (this.value.data[id] === undefined) this.value.data[id] = $.extend({}, this.elmsDefaults[type] || {});
					elmsInNextLayout.push(this.value[this.state].layout[plc][i]);
				}
			}
			for (elmId in this.value.data) {
				if (!this.value.data.hasOwnProperty(elmId)) continue;
				if (elmsInNextLayout.indexOf(elmId) == -1) this.value[this.state].layout.hidden.push(elmId);
			}
			// Retrieving the currently shown layout structure
			var prevLayout = {},
				parsePlace = function(place, $place){
					if ($place.hasClass('us-hb-editor-wrapper')) $place = $place.children('.us-hb-editor-wrapper-content');
					prevLayout[place] = [];
					$place.children().each(function(index, elm){
						var $elm = $(elm),
							id = $elm.data('id');
						if (!id) return;
						prevLayout[place].push(id);
					});
				};
			$.each(this.$places, parsePlace);
			$.each(this.$wrappers, parsePlace);
			// Iteratively looping through the needed structure
			for (plc in this.value[this.state].layout) {
				if (!this.value[this.state].layout.hasOwnProperty(plc)) continue;
				if (plc.indexOf(':') != -1 && prevLayout[plc] === undefined) {
					// Creating the missing wrapper
					if (this.$wrappers[plc] === undefined) {
						this._createElementPlaceholder(plc, this.value.data[plc]);
					}
					prevLayout[plc] = [];
				}
				var $place = (plc.indexOf(':') == -1) ? this.$places[plc] : this.$wrappers[plc].children('.us-hb-editor-wrapper-content');
				for (i = 0; i < this.value[this.state].layout[plc].length; i++) {
					elmId = this.value[this.state].layout[plc][i];
					var $elm = this[(elmId.substr(1, 7) == 'wrapper') ? '$wrappers' : '$elms'][elmId];
					if ($elm === undefined) {
						$elm = this._createElementPlaceholder(elmId, this.value.data[elmId]);
					}
					if (prevLayout[plc][i] != elmId) {
						if (i == 0) {
							$elm.prependTo($place);
						} else {
							var prevElmId = this.value[this.state].layout[plc][i - 1],
								$prevElm = this[(prevElmId.substr(1, 7) == 'wrapper') ? '$wrappers' : '$elms'][prevElmId];
							$elm.insertAfter($prevElm);
						}
						prevLayout[plc].splice(i, 0, elmId);
					}
				}
			}
			// Removing excess elements
			for (plc in prevLayout) {
				if (!prevLayout.hasOwnProperty(plc))continue;
				for (i = 0, elmId = prevLayout[plc][i]; i < prevLayout[plc].length; i++, elmId = prevLayout[plc][i]) {
					if (this.value.data[elmId] === undefined) this._removeElementPlaceholder(elmId);
				}
			}
			// Updating elements' placeholders contents
			for (elmId in this.$elms) {
				if (!this.$elms.hasOwnProperty(elmId)) continue;
				this._updateElementPlaceholder(this.$elms[elmId], elmId, this.value.data[elmId]);
			}
			// Fixing wrappers
			this.$container.find('.us-hb-editor-wrapper').removeClass('empty').find('.us-hb-editor-wrapper-content:empty').parent().addClass('empty');
		},

		/**
		 * Event that is called on manual side option change
		 * @param {$usof.Field} field
		 * @private
		 */
		_optionChanged: function(field){
			if (this.ignoreOptionsChanges) return;
			var fieldId = field.name,
				value = field.getValue(),
				state = ($.inArray(fieldId, this.sharedOptions) != -1) ? 'default' : this.state;
			if (this.value[state] === undefined) this.value[state] = {};
			if (this.value[state].options === undefined) this.value[state].options = {};
			this.value[state].options[fieldId] = value;
			this.renderOptions();
			this.trigger('change', this.value);
		},

		/**
		 * Change side options
		 * @param options
		 */
		setOptions: function(options){
			this.ignoreOptionsChanges = true;
			this.sideOptions.setValues(options);
			this.ignoreOptionsChanges = false;
			this.renderOptions();
		},

		/**
		 * Render current options
		 */
		renderOptions: function(){
			var prevOrientation = this.$editor.usMod('type'),
				nextOrientation = this.value[this.state].options.orientation || 'hor';
			if (nextOrientation != prevOrientation) {
				this.$editor.usMod('type', nextOrientation);
				if (nextOrientation == 'ver') {
					// Moving elements from removed cells to remaining ones
					if (this.value[this.state].layout.hidden === undefined) this.value[this.state].layout.hidden = [];
					for (var place in this.value[this.state].layout) {
						if (!this.value[this.state].layout.hasOwnProperty(place)) continue;
						if (place.indexOf(':') != -1 || place == 'hidden' || place.substr(place.length - 5) == '_left') continue;
						var align = place.split('_'),
							newPlace = (align.length == 2) ? (align[0] + '_left') : 'hidden';
						if (this.value[this.state].layout[newPlace] === undefined) this.value[this.state].layout[newPlace] = [];
						this.value[this.state].layout[newPlace] = this.value[this.state].layout[newPlace].concat(this.value[this.state].layout[place]);
						this.value[this.state].layout[place] = [];
					}
					this.renderLayout();
				}
			}
			$.each(['top', 'bottom'], function(index, vpos){
				var $row = this.$rows.filter('.at_' + vpos),
					prevShown = !$row.hasClass('disabled'),
					nextShown = !!parseInt(this.value[this.state].options[vpos + '_show']);
				if (prevShown != nextShown) {
					$row.toggleClass('disabled', !nextShown);
				}
			}.bind(this));
		}
	};

	/**
	 * USOF Field: Design Options
	 */
	$usof.field['design_options'] = {
		init: function(options){
			this.$input = this.$row.find('input');
			this.parentInit(options);
			this.$input.on('blur', function(e){
				var $target = $(e.target),
					rawValue = $target.val(),
					cleanValue = this._cleanValue(rawValue);
				if (rawValue != '' && rawValue != cleanValue) {
					$target.val(cleanValue);
					this._events.change();
				}
			}.bind(this));
		},
		_cleanValue: function(rawValue){
			if (rawValue === '') {
				return '';
			} else if (rawValue.indexOf('%') != -1) {
				return parseFloat(rawValue) + '%';
			} else if (/^[0-9](em|px)$/.exec(rawValue)) {
				return parseInt(rawValue) + rawValue.substr(rawValue.length - 2);
			} else {
				return parseInt(rawValue) + 'px';
			}
		},
		getValue: function(){
			var value = {};
			this.$input.each(function(index, input){
				var $input = $(input),
					name = $input.attr('name'),
					rawValue = $input.val();
				if (name == 'hide_for_sticky' || name == 'hide_for_not-sticky') return;
				if (rawValue !== '') value[name] = this._cleanValue(rawValue);
			}.bind(this));
			value['hide_for_sticky'] = this.$input.filter('[name="hide_for_sticky"]').is(':checked');
			value['hide_for_not-sticky'] = this.$input.filter('[name="hide_for_not-sticky"]').is(':checked');
			return value;
		},
		setValue: function(value){
			this.$input.each(function(index, input){
				var $input = $(input),
					name = $input.attr('name');
				if (name == 'hide_for_sticky' || name == 'hide_for_not-sticky') {
					if ($input.is('[type="checkbox"]')) $input.prop('checked', !!value[name]);
					return;
				}
				$input.val((value[name] === undefined) ? '' : value[name]);
			}.bind(this));
		}
	};
}(jQuery);

jQuery(function($){
	var USHB = function(container){
		this.$container = $(container);
		if (!this.$container.length) return;
		this.initFields(this.$container);

		this.fireFieldEvent(this.$container, 'beforeShow');
		this.fireFieldEvent(this.$container, 'afterShow');

		// Save action
		this.$saveControl = this.$container.find('.usof-control.for_save');
		this.$saveBtn = this.$saveControl.find('.usof-button').on('click', this.save.bind(this));
		this.$saveMessage = this.$saveControl.find('.usof-control-message');
		this.valuesChanged = {};
		this.saveStateTimer = null;
		for (var fieldId in this.fields) {
			if (!this.fields.hasOwnProperty(fieldId)) continue;
			this.fields[fieldId].on('change', function(field, value){
				if ($.isEmptyObject(this.valuesChanged)) {
					clearTimeout(this.saveStateTimer);
					this.$saveControl.usMod('status', 'notsaved');
				}
				this.valuesChanged[field.name] = value;
			}.bind(this));
		}
	};
	$.extend(USHB.prototype, $usof.mixins.Fieldset, {
		/**
		 * Save the new values
		 */
		save: function(){
			if ($.isEmptyObject(this.valuesChanged)) return;
			clearTimeout(this.saveStateTimer);
			this.$saveMessage.html('');
			this.$saveControl.usMod('status', 'loading');

			$.ajax({
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'ushb_save',
					ID: this.$container.data('id'),
					post_title: this.getValue('post_title'),
					post_content: JSON.stringify(this.getValue('post_content')),
					_wpnonce: this.$container.find('[name="_wpnonce"]').val(),
					_wp_http_referer: this.$container.find('[name="_wp_http_referer"]').val()
				},
				success: function(result){
					if (result.success) {
						this.valuesChanged = {};
						this.$saveMessage.html(result.data.message);
						this.$saveControl.usMod('status', 'success');
						this.saveStateTimer = setTimeout(function(){
							this.$saveMessage.html('');
							this.$saveControl.usMod('status', 'clear');
						}.bind(this), 4000);
					} else {
						this.$saveMessage.html(result.data.message);
						this.$saveControl.usMod('status', 'error');
						this.saveStateTimer = setTimeout(function(){
							this.$saveMessage.html('');
							this.$saveControl.usMod('status', 'notsaved');
						}.bind(this), 4000);
					}
				}.bind(this)
			});
		}
	});

	new USHB('.usof-container.type_hb');

	// Pencil icon hear the header edit
	var $headerTitle = $('input[name="post_title"]'),
		$headerEditIcon = $('<span class="usof-form-row-control-icon"></span>').text($headerTitle.val()).insertAfter($headerTitle);
	$headerTitle.on('change keyup', function(){
		$headerEditIcon.text($headerTitle.val() || $headerTitle.attr('placeholder'));
	});
});
