<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 26/03/2018
 */

namespace Octopouce\AdminBundle\Utils;


use Octopouce\AdminBundle\Service\Transformer\OptionTransformer;

class MailerService {

	private $options;

	/**
	 * MailerService constructor.
	 *
	 * @param OptionTransformer $optionTransformer
	 */
	public function __construct(OptionTransformer $optionTransformer) {
		$this->options = $optionTransformer->getOptionsWithKeyName();
	}

	public function send($to = 'kevin@octopouce.mu', $subject = 'This is a test', $body = 'This is a test.'){

		$transport = (new \Swift_SmtpTransport($this->options['MAIL_SMTP_HOST']->getValue(), intval($this->options['MAIL_SMTP_PORT']->getValue())))
			->setUsername($this->options['MAIL_SMTP_USERNAME']->getValue())
			->setPassword($this->options['MAIL_SMTP_PASSWORD']->getValue())
		;

		$mailer = new \Swift_Mailer($transport);

		$message = (new \Swift_Message($subject))
			->setFrom([$this->options['MAIL_FROM_EMAIL']->getValue() => $this->options['MAIL_FROM_NAME']->getValue()])
			->setTo($to)
			->setBody($body, 'text/html')
		;

		if($this->options['MAIL_TO_DEV']->getValue() && (getenv('APP_ENV') == 'dev' || getenv('APP_ENV') == 'test')){
			$message->setTo($this->options['MAIL_TO_DEV']->getValue());
		}

		$mailer->send($message);
	}


}