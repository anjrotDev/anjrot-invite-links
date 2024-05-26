import { __ } from "@wordpress/i18n";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { PanelBody, TextControl, ToggleControl } from "@wordpress/components";

const Edit = (props) => {
	const { attributes, setAttributes } = props;
	const { label, placeholder, name, required, fullWidth, width } = attributes;

	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__("Settings", "text-input")}>
					<ToggleControl
						label={__("Show Label", "text-input")}
						checked={!!label}
						onChange={(showLabel) =>
							setAttributes({ label: showLabel ? "Label" : "" })
						}
					/>
					{label && (
						<TextControl
							label={__("Label Text", "text-input")}
							value={label}
							onChange={(newLabel) => setAttributes({ label: newLabel })}
						/>
					)}
					<TextControl
						label={__("Placeholder", "text-input")}
						value={placeholder}
						onChange={(newPlaceholder) =>
							setAttributes({ placeholder: newPlaceholder })
						}
					/>
					<TextControl
						label={__("Name Attribute", "text-input")}
						value={name}
						onChange={(newName) => setAttributes({ name: newName })}
					/>
					<ToggleControl
						label={__("Required", "text-input")}
						checked={required}
						onChange={(newRequired) => setAttributes({ required: newRequired })}
					/>
					<ToggleControl
						label={__("Full Width", "text-input")}
						checked={fullWidth}
						onChange={(isFullWidth) => {
							setAttributes({ fullWidth: isFullWidth });
							if (isFullWidth) {
								setAttributes({ width: "100%" });
							}
						}}
					/>
					{!fullWidth && (
						<TextControl
							label={__("Width (e.g., 50%, 200px)", "text-input")}
							value={width}
							onChange={(newWidth) => setAttributes({ width: newWidth })}
						/>
					)}
				</PanelBody>
			</InspectorControls>
			{label && <label>{label}</label>}
			<input
				type="text"
				placeholder={placeholder}
				name={name}
				required={required}
				style={{ width: fullWidth ? "100%" : width }}
			/>
		</div>
	);
};

export default Edit;
