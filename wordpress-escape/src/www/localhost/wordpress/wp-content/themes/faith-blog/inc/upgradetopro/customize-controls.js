(function (api) {

	// Extends our custom "Faith Blog" section.
	api.sectionConstructor['faith-blog'] = api.Section.extend({

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	});
	jQuery("#accordion-panel-blog-starter-theme-options").addClass("custom-class");

})(wp.customize);