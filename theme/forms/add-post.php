<?php

use Nette\Forms\Form;

$form = new Form;

$form->addProtection('Detected robot activity.');

$form->addReCaptcha('captcha', NULL, 'Prokaž, že nejsi robot.');

$form->addText('title', 'Nadpis:')
	->setRequired('Musíš svůj článek nějak nazvat.');

$form->addTextarea('content', 'Obsah:')
	->setRequired('Napiš nějaký text.');

$form->addUpload('thumb', 'Náhledový obrázek:')
	->setRequired(FALSE)
	->addRule(Form::IMAGE, 'Obrázek musí být JPEG, PNG nebo GIF.');

$categories = get_categories(array("hide_empty" => false));
$options = [];
foreach ($categories as $category) {
	$options[$category->term_id] =  $category->name;
}

$form->addCheckboxList('category', 'Kategorie:', $options);

$form->addSubmit('send', 'Odeslat ke schválení');

if(isFormValid($form, __FILE__)) {
	$values = $form->getValues();

	if ( is_user_logged_in() ) {
		$post_status = 'publish';
	} else {
		$post_status = 'pending';
	}

	try {
		$post_information = array(
			'post_title'    => wp_strip_all_tags( $values['title'] ),
			'post_content'  => $values['content'],
			'post_type'     => 'post',
			'post_status'   => $post_status,
			'post_category' => $values['category']
		);

		$post_id = wp_insert_post( $post_information );

		if (!empty($values['thumb'])) {
			$uploaddir = wp_upload_dir();
			$filename = basename($values['thumb']->name);

			if ( wp_mkdir_p( $upload_dir['path'] ) ) {
				$file = $uploaddir['path'] . '/' . $filename;
			} else {
				$file = $uploaddir['basedir'] . '/' . $filename;
			}

			move_uploaded_file( $values['thumb']->getTemporaryFile(), $file );

			$wp_filetype = wp_check_filetype( $filename, null );

			$attachment  = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => sanitize_file_name( $filename ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			$attach_id   = wp_insert_attachment( $attachment, $file, $post_id );

			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			$res1        = wp_update_attachment_metadata( $attach_id, $attach_data );
			$res2        = set_post_thumbnail( $post_id, $attach_id );
		}

		if ($post_id) {
			wp_redirect(add_query_arg('success', true, remove_query_arg('do')));
			die;
		}
	} catch (SendException $e) {
		$form->addError('Něco se pokazilo. Zkuste to znovu');
		wp_redirect(add_query_arg('success', false, remove_query_arg('do')));
	}
}

return $form;
