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

if ( !is_user_logged_in() ) {
	$form->addText('nickname', 'Přezdívka:')
		->setRequired('Napiš nám sem svou přezdívku.');

	$form->addText('mail', 'E-mail:')
		->addRule(Form::EMAIL, 'Napiš e-mail ve správném tvaru.')
		->setRequired('Napiš nám sem svůj e-mail.');
}

$form->addSubmit('send', 'Odeslat ke schválení');

if(isFormValid($form, __FILE__)) {
	$values = $form->getValues();

	if ( is_user_logged_in() ) {
		$post_status = 'publish';
		$meta = '';
		$user = get_current_user_id();
	} else {
		$post_status = 'pending';
		$meta = ['praha_post_author_name' => $values['nickname'], 'praha_post_author_mail' => $values['mail']];
		$user = get_user_by('login', 'frontend')->ID;
	}

	try {
		$post_information = array(
			'post_title'    => wp_strip_all_tags( $values['title'] ),
			'post_content'  => $values['content'],
			'post_type'     => 'post',
			'post_status'   => $post_status,
			'post_category' => $values['category'],
			'post_author'   => $user,
			'meta_input'    => $meta
		);

		$post_id = wp_insert_post( $post_information );

		if (!empty($values['thumb']->name)) {

			$file = $values['thumb'];

			global $post;

			if ($file instanceof  Nette\Http\FileUpload) {
				if($file->isOk()) {
					$name = uniqid();
					$upload = wp_upload_bits(wp_unique_filename(wp_upload_dir()['path'], $file->getName()), null, $file->getContents());
					if($upload['error']) {
						trigger_error('Upload failed: ' . $upload['error']);
					} else {
						$attach_id = wp_insert_attachment([
							'post_title' => $name,
							'post_content' => '',
							'post_status' => 'inherit',
							'post_mime_type' => $file->getContentType()
						], $upload['file'], $post_id);
					}
				}
			}

			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
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
