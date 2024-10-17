import { __ } from "@wordpress/i18n";
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
	AlignmentToolbar,
	PanelColorSettings,
	InnerBlocks,
} from "@wordpress/block-editor";
import {
	PanelBody,
	TextControl,
	RangeControl,
	ToggleControl,
	SelectControl,
} from "@wordpress/components";

const Edit = (props) => {
	const { attributes, setAttributes } = props;
	const {
		headingText,
		textAlign,
		textColor,
		backgroundColor,
		borderColor,
		borderWidth,
		borderRadius,
		padding,
		buttonText,
		buttonTextColor,
		buttonBackgroundColor,
		buttonBorderRadius,
		buttonFullWidth,
		buttonWidth,
		buttonBorderColor,
		buttonBorderWidth,
		buttonPadding,
		submitAction,
		redirectUrl,
		confirmationMessage,
		emailTo,
		emailCc,
		emailSubject,
	} = attributes;

	const blockProps = useBlockProps({
		style: {
			textAlign: textAlign,
			color: textColor,
			backgroundColor: backgroundColor,
			borderColor: borderColor,
			borderWidth: borderWidth,
			borderRadius: borderRadius,
			padding: padding + "px",
			borderStyle: "solid",
		},
	});

	const buttonStyle = {
		color: buttonTextColor,
		backgroundColor: buttonBackgroundColor,
		borderRadius: buttonBorderRadius + "px",
		borderColor: buttonBorderColor,
		borderWidth: buttonBorderWidth + "px",
		padding: buttonPadding + "px",
		borderStyle: "solid",
		width: buttonFullWidth ? "100%" : buttonWidth,
	};

	return (
		<div {...blockProps}>
			<BlockControls>
				<AlignmentToolbar
					value={textAlign}
					onChange={(newAlign) => setAttributes({ textAlign: newAlign })}
				/>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={__("Settings", "anjrot-invite-links")}>
					<TextControl
						label={__("Heading Text", "anjrot-invite-links")}
						value={headingText}
						onChange={(newText) => setAttributes({ headingText: newText })}
					/>
				</PanelBody>
				<PanelColorSettings
					title={__("Color Settings", "anjrot-invite-links")}
					initialOpen={false}
					colorSettings={[
						{
							value: textColor,
							onChange: (color) => setAttributes({ textColor: color }),
							label: __("Text Color", "anjrot-invite-links"),
						},
						{
							value: backgroundColor,
							onChange: (color) => setAttributes({ backgroundColor: color }),
							label: __("Background Color", "anjrot-invite-links"),
						},
						{
							value: borderColor,
							onChange: (color) => setAttributes({ borderColor: color }),
							label: __("Border Color", "anjrot-invite-links"),
						},
					]}
				/>
				<PanelBody title={__("Border Settings", "anjrot-invite-links")}>
					<RangeControl
						label={__("Border Width", "anjrot-invite-links")}
						value={borderWidth}
						onChange={(value) => setAttributes({ borderWidth: value })}
						min={0}
						max={10}
					/>
					<RangeControl
						label={__("Border Radius", "anjrot-invite-links")}
						value={borderRadius}
						onChange={(value) => setAttributes({ borderRadius: value })}
						min={0}
						max={50}
					/>
					<RangeControl
						label={__("Padding", "anjrot-invite-links")}
						value={padding}
						onChange={(value) => setAttributes({ padding: value })}
						min={0}
						max={50}
					/>
				</PanelBody>
				<PanelBody title={__("Button Settings", "anjrot-invite-links")}>
					<TextControl
						label={__("Button Text", "anjrot-invite-links")}
						value={buttonText}
						onChange={(newText) => setAttributes({ buttonText: newText })}
					/>
					<PanelColorSettings
						title={__("Button Colors", "anjrot-invite-links")}
						initialOpen={false}
						colorSettings={[
							{
								value: buttonTextColor,
								onChange: (color) => setAttributes({ buttonTextColor: color }),
								label: __("Button Text Color", "anjrot-invite-links"),
							},
							{
								value: buttonBackgroundColor,
								onChange: (color) =>
									setAttributes({ buttonBackgroundColor: color }),
								label: __("Button Background Color", "anjrot-invite-links"),
							},
						]}
					/>
					<RangeControl
						label={__("Button Padding", "anjrot-invite-links")}
						value={buttonPadding}
						onChange={(value) => setAttributes({ buttonPadding: value })}
						min={0}
						max={50}
					/>
					<RangeControl
						label={__("Button Border Radius", "anjrot-invite-links")}
						value={buttonBorderRadius}
						onChange={(value) => setAttributes({ buttonBorderRadius: value })}
						min={0}
						max={50}
					/>
					<PanelColorSettings
						title={__("Button Border Color", "anjrot-invite-links")}
						initialOpen={false}
						colorSettings={[
							{
								value: buttonBorderColor,
								onChange: (color) =>
									setAttributes({ buttonBorderColor: color }),
								label: __("Button Border Color", "anjrot-invite-links"),
							},
						]}
					/>
					<RangeControl
						label={__("Button Border Width", "anjrot-invite-links")}
						value={buttonBorderWidth}
						onChange={(value) => setAttributes({ buttonBorderWidth: value })}
						min={0}
						max={10}
					/>
					<ToggleControl
						label={__("Button Full Width", "anjrot-invite-links")}
						checked={buttonFullWidth}
						onChange={(value) => setAttributes({ buttonFullWidth: value })}
					/>
					{!buttonFullWidth && (
						<TextControl
							label={__(
								"Button Width (e.g., 50%, 200px)",
								"anjrot-invite-links",
							)}
							value={buttonWidth}
							onChange={(newWidth) => setAttributes({ buttonWidth: newWidth })}
							help={__("Leave empty for auto width", "anjrot-invite-links")}
						/>
					)}
				</PanelBody>
				<PanelBody title={__("Email Settings", "anjrot-invite-links")}>
					<TextControl
						label={__("To Email", "anjrot-invite-links")}
						value={emailTo}
						onChange={(value) => setAttributes({ emailTo: value })}
					/>
					<TextControl
						label={__("CC Email", "anjrot-invite-links")}
						value={emailCc}
						onChange={(value) => setAttributes({ emailCc: value })}
					/>
					<TextControl
						label={__("Email Subject", "anjrot-invite-links")}
						value={emailSubject}
						onChange={(value) => setAttributes({ emailSubject: value })}
					/>
					<SelectControl
						label={__("Submit Action", "anjrot-invite-links")}
						value={submitAction}
						options={[
							{
								label: __("Send Email", "anjrot-invite-links"),
								value: "sendEmail",
							},
							{
								label: __("Show Message", "anjrot-invite-links"),
								value: "showMessage",
							},
							{
								label: __("Send to API", "anjrot-invite-links"),
								value: "sendToAPI",
							},
						]}
						onChange={(value) => setAttributes({ submitAction: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<h2 style={{ textAlign, color: textColor }}>{headingText}</h2>
			<InnerBlocks
				allowedBlocks={[
					"core/columns",
					"anjrot-invite-links/text-input",
					"anjrot-invite-links/email-input",
				]}
			/>
			<button type="submit" style={buttonStyle}>
				{buttonText}
			</button>
		</div>
	);
};

export default Edit;
