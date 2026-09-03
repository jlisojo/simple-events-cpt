/**
 * Gutenberg Block: Event Grid
 * Renders an interactive event grid block in the WordPress editor.
 */
(function (blocks, element, blockEditor, components, serverSideRender, i18n) {
    var __ = i18n.__;
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var RangeControl = components.RangeControl;
    var ToggleControl = components.ToggleControl;
    var ServerSideRender = serverSideRender;

    registerBlockType('simple-events-cpt/event-grid', {
        title: __('Event Grid', 'simple-events-cpt'),
        description: __('Display upcoming events in a responsive grid layout.', 'simple-events-cpt'),
        icon: 'calendar-alt',
        category: 'widgets',
        keywords: [__('events', 'simple-events-cpt'), __('calendar', 'simple-events-cpt'), __('grid', 'simple-events-cpt')],
        attributes: {
            count: {
                type: 'number',
                default: 3
            },
            title: {
                type: 'string',
                default: ''
            },
            show_button: {
                type: 'boolean',
                default: true
            },
            button_text: {
                type: 'string',
                default: __('View All Events', 'simple-events-cpt')
            },
            button_url: {
                type: 'string',
                default: ''
            }
        },
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('Event Grid Settings', 'simple-events-cpt'), initialOpen: true },
                        el(TextControl, {
                            label: __('Grid Title', 'simple-events-cpt'),
                            value: attributes.title,
                            onChange: function (value) {
                                setAttributes({ title: value });
                            }
                        }),
                        el(RangeControl, {
                            label: __('Number of Events', 'simple-events-cpt'),
                            value: attributes.count,
                            onChange: function (value) {
                                setAttributes({ count: value });
                            },
                            min: 1,
                            max: 12
                        }),
                        el(ToggleControl, {
                            label: __('Show "View All" Button', 'simple-events-cpt'),
                            checked: attributes.show_button,
                            onChange: function (value) {
                                setAttributes({ show_button: value });
                            }
                        }),
                        attributes.show_button && el(TextControl, {
                            label: __('Button Text', 'simple-events-cpt'),
                            value: attributes.button_text,
                            onChange: function (value) {
                                setAttributes({ button_text: value });
                            }
                        }),
                        attributes.show_button && el(TextControl, {
                            label: __('Button Custom URL (optional)', 'simple-events-cpt'),
                            value: attributes.button_url,
                            placeholder: __('Leave empty for default archive URL', 'simple-events-cpt'),
                            onChange: function (value) {
                                setAttributes({ button_url: value });
                            }
                        })
                    )
                ),
                el('div', { key: 'render', className: 'simple-events-block-preview' },
                    el(ServerSideRender, {
                        block: 'simple-events-cpt/event-grid',
                        attributes: attributes
                    })
                )
            ];
        },
        save: function () {
            // Rendered dynamically on server side in PHP
            return null;
        }
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor || window.wp.editor,
    window.wp.components,
    window.wp.serverSideRender || window.wp.components.ServerSideRender,
    window.wp.i18n
);
