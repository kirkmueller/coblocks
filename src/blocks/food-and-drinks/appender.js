/**
 * External dependencies.
 */
import { noop } from 'lodash';

/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { IconButton } from '@wordpress/components';

const CustomAppender = ( { onClick = noop } ) => {
	return (
		<div className="coblocks-list-appender">
			<IconButton
				icon="insert"
				label={ __( 'Add Menu Section', 'coblocks' ) }
				labelPosition="bottom"
				className="coblocks-list-appender__toggle"
				onClick={ onClick }
			>
				{ __( 'Add Menu Section', 'coblocks' ) }
			</IconButton>
		</div>
	);
};

export default CustomAppender;
