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
				<PanelBody title={__("Settings", "email-input")}>
					<ToggleControl
						label={__("Show Label", "email-input")}
						checked={!!label}
						onChange={(showLabel) =>
							setAttributes({ label: showLabel ? "Label" : "" })
						}
					/>
					{label && (
						<TextControl
							label={__("Label Text", "email-input")}
							value={label}
							onChange={(newLabel) => setAttributes({ label: newLabel })}
						/>
					)}
					<TextControl
						label={__("Placeholder", "email-input")}
						value={placeholder}
						onChange={(newPlaceholder) =>
							setAttributes({ placeholder: newPlaceholder })
						}
					/>
					<TextControl
						label={__("Name", "email-input")}
						value={name}
						onChange={(newName) => setAttributes({ name: newName })}
					/>
					<ToggleControl
						label={__("Required", "email-input")}
						checked={required}
						onChange={(newRequired) => setAttributes({ required: newRequired })}
					/>
					<ToggleControl
						label={__("Full Width", "email-input")}
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
							label={__("Width (e.g., 50%, 200px)", "email-input")}
							value={width}
							onChange={(newWidth) => setAttributes({ width: newWidth })}
						/>
					)}
				</PanelBody>
			</InspectorControls>
			{label && <label>{label}</label>}
			<input
				type="email"
				placeholder={placeholder}
				name={name}
				required={required}
				style={{ width: fullWidth ? "100%" : width }}
			/>
		</div>
	);
};

export default Edit;
