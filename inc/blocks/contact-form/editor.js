/**
 * Contact Form Block Editor Script
 */
(function () {
  const { registerBlockType } = wp.blocks;
  const { InspectorControls, useBlockProps } = wp.blockEditor;
  const { PanelBody, TextControl, ToggleControl } = wp.components;
  const { __ } = wp.i18n;
  const { createElement: el } = wp.element;

  registerBlockType("bigtricks/contact-form", {
    edit: function (props) {
      const { attributes, setAttributes } = props;
      const blockProps = useBlockProps({
        className: "bigtricks-contact-form-editor",
      });

      return el(
        "div",
        blockProps,
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Form Settings", "bigtricks"), initialOpen: true },
            el(ToggleControl, {
              label: __("Show Title", "bigtricks"),
              checked: attributes.showTitle,
              onChange: function (value) {
                setAttributes({ showTitle: value });
              },
            }),
            el(ToggleControl, {
              label: __("Show Description", "bigtricks"),
              checked: attributes.showDescription,
              onChange: function (value) {
                setAttributes({ showDescription: value });
              },
            }),
            el(TextControl, {
              label: __("Form Title", "bigtricks"),
              value: attributes.formTitle,
              onChange: function (value) {
                setAttributes({ formTitle: value });
              },
            }),
            el(TextControl, {
              label: __("Form Description", "bigtricks"),
              value: attributes.formDescription,
              onChange: function (value) {
                setAttributes({ formDescription: value });
              },
            }),
            el(TextControl, {
              label: __("Submit Button Text", "bigtricks"),
              value: attributes.submitButtonText,
              onChange: function (value) {
                setAttributes({ submitButtonText: value });
              },
            }),
            el(TextControl, {
              label: __("Success Message", "bigtricks"),
              value: attributes.successMessage,
              onChange: function (value) {
                setAttributes({ successMessage: value });
              },
            }),
          ),
        ),
        el(
          "div",
          {
            className:
              "max-w-2xl mx-auto p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300",
          },
          el(
            "div",
            { className: "text-center mb-4" },
            el("span", {
              className: "dashicons dashicons-email text-4xl text-gray-400",
            }),
          ),
          attributes.showTitle &&
            el(
              "h2",
              {
                className: "text-2xl font-bold text-gray-900 mb-3 text-center",
              },
              attributes.formTitle,
            ),
          attributes.showDescription &&
            el(
              "p",
              { className: "text-gray-600 mb-6 text-center" },
              attributes.formDescription,
            ),
          el(
            "div",
            { className: "bg-white p-6 rounded-lg border border-gray-200" },
            el(
              "p",
              { className: "text-sm text-gray-500 text-center mb-4" },
              "📝 Contact Form Preview",
            ),
            el(
              "div",
              { className: "space-y-4" },
              el(
                "div",
                {},
                el(
                  "label",
                  { className: "block text-sm font-semibold mb-2" },
                  "Your Name *",
                ),
                el(
                  "div",
                  {
                    className:
                      "border border-gray-300 rounded-lg px-4 py-3 bg-gray-50",
                  },
                  "Name field",
                ),
              ),
              el(
                "div",
                {},
                el(
                  "label",
                  { className: "block text-sm font-semibold mb-2" },
                  "Email Address *",
                ),
                el(
                  "div",
                  {
                    className:
                      "border border-gray-300 rounded-lg px-4 py-3 bg-gray-50",
                  },
                  "Email field",
                ),
              ),
              el(
                "div",
                {},
                el(
                  "label",
                  { className: "block text-sm font-semibold mb-2" },
                  "WhatsApp Number",
                ),
                el(
                  "div",
                  {
                    className:
                      "border border-gray-300 rounded-lg px-4 py-3 bg-gray-50",
                  },
                  "Phone field",
                ),
              ),
              el(
                "div",
                {},
                el(
                  "label",
                  { className: "block text-sm font-semibold mb-2" },
                  "Your Message *",
                ),
                el(
                  "div",
                  {
                    className:
                      "border border-gray-300 rounded-lg px-4 py-3 bg-gray-50 h-24",
                  },
                  "Message field",
                ),
              ),
              el(
                "button",
                {
                  className:
                    "w-full bg-indigo-600 text-white font-semibold py-3 rounded-lg",
                  disabled: true,
                },
                attributes.submitButtonText,
              ),
            ),
          ),
          el(
            "p",
            { className: "text-xs text-gray-500 text-center mt-4" },
            "Configure form settings in the right sidebar →",
          ),
        ),
      );
    },
    save: function () {
      return null; // Dynamic block, rendered via PHP
    },
  });
})();
