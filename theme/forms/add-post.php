<?php

use Nette\Forms\Form;
use Nette\Utils\Html;


$form = new Form;

$form->addProtection('Detected robot activity.');

$form->addText('title', 'Nadpis:')
	->setRequired('Musíš svůj článek nějak nazvat.')
	->addRule($form::MAX_LENGTH, "Název článku nemůže být delší nšž 40 znaků.", 40);

$form->addTextarea('content', 'Obsah:')
	->setRequired('Napiš nějaký text.');

$form->addUpload('thumb', HTML::el('span')->addHTML('Náhledový obrázek:<br><small>(čtverec alespoň 660×660px)</small>'))
	->setRequired(FALSE)
	->addRule(Form::IMAGE, 'Obrázek musí být JPEG, PNG nebo GIF.');

$categories = get_categories(['hide_empty' => false]);
$options = [];
foreach ($categories as $category) {
	$options[$category->term_id] =  $category->name;
}

$form->addCheckboxList('category', 'Kategorie:', $options);

if (is_user_logged_in()) {
	$form->addCheckbox('sticky', 'Zvýraznit');
}

if (!is_user_logged_in()) {
	$form->addReCaptcha('captcha', NULL, 'Prokaž, že nejsi robot.');

	$form->addText('nickname', 'Přezdívka:')
		->setRequired('Napiš nám sem svou přezdívku.');

	$form->addText('mail', 'E-mail:')
		->addRule(Form::EMAIL, 'Napiš e-mail ve správném tvaru.')
		->setRequired('Napiš nám sem svůj e-mail.');
}

$form->addSubmit('send', 'Odeslat ke schválení');

if(isFormValid($form, __FILE__)) {
	$values = $form->getValues();

	if (is_user_logged_in()) {
		$post_status = 'publish';
		$meta = '';
		$user = get_current_user_id();
	} else {
		$post_status = 'pending';
		$meta = ['praha_post_author_name' => $values['nickname'], 'praha_post_author_mail' => $values['mail']];
		$user = get_user_by('login', 'frontend')->ID;
	}

	try {
		$post_information = [
			'post_title'    => wp_strip_all_tags($values['title']),
			'post_content'  => $values['content'],
			'post_type'     => 'post',
			'post_status'   => $post_status,
			'post_category' => $values['category'],
			'post_author'   => $user,
			'meta_input'    => $meta
		];

		$post_id = wp_insert_post($post_information);

		if (is_user_logged_in() && $values['sticky']) {
			stick_post($post_id);
		}

		if (!is_user_logged_in()) {
			$body = 'Jupí! Na náš krajský web někdo přidal příspěvek.<br>';
			$body .= 'Co nejdřív na něj jukni ať není ' . $values['nickname'] . ' nervózní.<br>';
			$body .= 'Kdyby něco neštymovalo, tak mu napiš na mail <a href="mailto:' . $values['mail'] . '" target="_blank">' . $values['mail'] . '</a>.<br>';
			$body .= 'Když bude všechno v pohodě, tak článek publikuj v <a href="' . home_url('api/editpost/' . $post_id) . '" target="_blank">administraci</a>.<br><br>';
			$body .= '<h1>' . wp_strip_all_tags($values['title']) . '</h1>';
			$body .= $values['content'];

			$email = new \Nette\Mail\Message();

			$email->setFrom('web@praha.skauting.cz')
			->addTo(get_option('admin_email'))
			->setSubject('Nový příspěvek na webu praha.skauting.cz')
			->setHtmlBody($body);

			$mailer = $App->mailer;
			$mailer->send($email);
		}

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

			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
			$res1        = wp_update_attachment_metadata($attach_id, $attach_data);
			$res2        = set_post_thumbnail($post_id, $attach_id);
		}

		if ($post_id) {
			wp_redirect(add_query_arg('success', true, get_home_url()));
			die;
		}
	} catch (SendException $e) {
		$form->addError('Něco se pokazilo. Zkuste to znovu');
		wp_redirect(add_query_arg('success', false, get_home_url()));
	}
}

return $form;
