import { useBlockProps } from "@wordpress/block-editor";

const Save = (props) => {
	const { attributes } = props;
	const { label, placeholder, name, required, fullWidth, width } = attributes;

	const blockProps = useBlockProps.save();

	return (
		<div {...blockProps}>
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

export default Save;
