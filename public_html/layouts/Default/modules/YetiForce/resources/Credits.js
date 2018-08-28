/* {[The file is published on the basis of YetiForce Public License 3.0 that can be found in the following directory: licenses/LicenseEN.txt or yetiforce.com]} */
'use strict';

jQuery.Class("YetiForce_Credits_Js", {}, {
	/**
	 *
	 * @returns {jQuery}
	 */
	getContainer: function () {
		if (!this.container) {
			this.container = $('.js-table-container');
		}
		return this.container;
	},
	/**
	 *
	 * @param contentData
	 * @returns {jQuery}
	 */
	registerDataTables: function (contentData) {
		$.extend($.fn.dataTable.defaults, {
			bPaginate: false,
			order: [],
			language: {
				sZeroRecords: app.translate('JS_NO_RESULTS_FOUND'),
				sInfo: app.translate('JS_S_INFO'),
				sInfoEmpty: app.translate('JS_S_INFO_EMPTY'),
				sSearch: app.translate('JS_SEARCH'),
				sEmptyTable: app.translate('JS_NO_RESULTS_FOUND'),
				sInfoFiltered: app.translate('JS_S_INFO_FILTERED'),
				sLoadingRecords: app.translate('JS_LOADING_OF_RECORDS'),
				sProcessing: app.translate('JS_LOADING_OF_RECORDS'),
				oAria: {
					sSortAscending: app.translate('JS_S_SORT_ASCENDING'),
					sSortDescending: app.translate('JS_S_SORT_DESCENDING')
				}
			}
		});
		return contentData.find('.dataTableWithRecords').DataTable();
	},
	/**
	 *
	 * @param container
	 */
	showMore: function (container) {
		container.find('.js-show-more').on('click', function (e) {
			AppConnector.request({
				module: app.getModuleName(),
				parent: app.getParentModuleName(),
				view: 'LibraryMoreInfo',
				type: $(this).attr('data-type'),
				libraryName: $(this).attr('data-library-name')
			}).done(function (data) {
				let html = '<div class="modal fade modal' + name + '" id="modal' + name + '"><div class="modal-dialog modal-lg"><div class="modal-content">';
				html += data;
				html += '</div></div></div><div>';
				app.showModalWindow(html);
			});
		});
	},
	/**
	 *
	 * @param container
	 */
	showLicense: function (container) {
		container.find('.js-show-license').on('click', function (e) {
			AppConnector.request({
				module: app.getModuleName(),
				parent: app.getParentModuleName(),
				view: 'LibraryLicense',
				license: $(this).attr('data-license'),
			}).done(function (data) {
				if (data) {
					let html = '<div class="modal fade modal' + name + '" id="modal' + name + '"><div class="modal-dialog modal-lg"><div class="modal-content">';
					html += data;
					html += '</div></div></div><div>';
					app.showModalWindow(html);
				}
			});
		});
	},
	registerEvents: function () {
		let container = this.getContainer();
		this.registerDataTables(container);
		this.showMore(container);
		this.showLicense(container);
	}
});
