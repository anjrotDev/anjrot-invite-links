import { useBlockProps, InnerBlocks } from "@wordpress/block-editor";

const Save = (props) => {
	const { attributes } = props;
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
		buttonBorderColor,
		buttonBorderWidth,
		buttonPadding,
		buttonWidth,
		submitAction,
		confirmationMessage,
		redirectUrl,
	} = attributes;

	const blockProps = useBlockProps.save({
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
		borderStyle: "solid",
		padding: buttonPadding,
		width: buttonFullWidth ? "100%" : buttonWidth,
	};

	return (
		<div {...blockProps} className="custom-form-container">
			<form
				method="POST"
				data-submit-action={submitAction}
				data-confirmation-message={confirmationMessage}
				data-redirect-url={redirectUrl}
			>
				<h2 style={{ textAlign, color: textColor }}>{headingText}</h2>
				<InnerBlocks.Content />
				<button type="submit" style={buttonStyle}>
					{buttonText}
				</button>
			</form>
		</div>
	);
};

export default Save;
