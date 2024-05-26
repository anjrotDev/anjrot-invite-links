import { registerBlockType, getBlockType } from "@wordpress/blocks";
import "./style.scss";
import Edit from "./edit";
import Save from "./save";
import metadata from "./block.json";

if (!getBlockType(metadata.name)) {
	registerBlockType(metadata.name, {
		title: metadata.title,
		edit: Edit,
		save: Save,
	});
}
