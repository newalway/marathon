<?php

namespace ProjectBundle\Twig;
use ProjectBundle\Utils\Collections;
use ProjectBundle\Utils\Products;

use ProjectBundle\Entity\SettingOption;

class AppExtension extends \Twig_Extension
{
	private $container;

    public function __construct($kernel)
    {
        $this->container = $kernel->getContainer();
    }

	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('getCustomerPlan', [$this, 'getCustomerPlan']),
			new \Twig_SimpleFunction('getPriceData', [$this, 'getPriceData']),
			new \Twig_SimpleFunction('getTrackingURL', [$this, 'getTrackingURL']),
			new \Twig_SimpleFunction('fosResettingEmailSubject', [$this, 'fos_resetting_email_subject']),
			new \Twig_SimpleFunction('fosResettingEmailMessage', [$this, 'fos_resetting_email_message']),
		);
	}

	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('slug', array($this, 'slugFilter')),
			new \Twig_SimpleFilter('gender', array($this, 'genderFilter')),
			new \Twig_SimpleFilter('status', array($this, 'statusFilter')),
			new \Twig_SimpleFilter('statusAvailable', array($this, 'statusAvailableFilter')),
			new \Twig_SimpleFilter('couponStatusText', array($this, 'couponStatusText')),
			new \Twig_SimpleFilter('getPercentProductDiscount', array($this, 'getPercentProductDiscount')),
			new \Twig_SimpleFilter('paymentstatus', array($this, 'paymentstatusFilter')),
		);
	}

	public function slugFilter($slug)
	{
		// Remove HTML tags
		$slug = preg_replace('/<(.*?)>/u', '', $slug);
		// Remove inner-word punctuation.
		$slug = preg_replace('/[\'"‘’“”]/u', '', $slug);
		// Make it lowercase
		$slug = mb_strtolower($slug, 'UTF-8');
		// remove space
		$slug = preg_replace(array('/\s{2,}/', '/[\t\n]/', '/\s+/'), '-', $slug);
		return $slug;
	}

	public function fos_resetting_email_subject($user)
	{
		$repository = $this->container->get('doctrine')->getEntityManager()->getRepository(SettingOption::class);
		$email_subject_value = '';
		$email_subject = $repository->findOneByOptionName('fos_resetting_email_subject');
		if($email_subject){
			$email_subject_value = $email_subject->getOptionValue();
		}

		$patterns = $this->container->getParameter('param_resetting_email_pattern');
		$value_array['email'] = $user->getEmail();
		$value_array['first_name'] = $user->getFirstName();
		$value_array['last_name'] = $user->getLastName();
		$email_subject_value = preg_replace($patterns, $value_array, $email_subject_value);

		return $email_subject_value;
	}

	public function fos_resetting_email_message($user, $confirmationUrl)
	{
		$repository = $this->container->get('doctrine')->getEntityManager()->getRepository(SettingOption::class);
		$email_message_value = '';
		$email_message = $repository->findOneByOptionName('fos_resetting_email_message');
		if($email_message){
			$email_message_value = $email_message->getOptionValue();
		}

		$patterns = $this->container->getParameter('param_resetting_email_pattern');
		$value_array['email'] = $user->getEmail();
		$value_array['first_name'] = $user->getFirstName();
		$value_array['last_name'] = $user->getLastName();
		$value_array['confirmation_url'] = $confirmationUrl;
		$email_message_value = preg_replace($patterns, $value_array, $email_message_value);

		return $email_message_value;
	}

	public function getCustomerPlan($member)
	{
		return Collections::getCustomerPlan($member);
	}

	public function genderFilter($gender)
	{
		return Collections::wordGender($gender);
	}

	public function statusFilter($status)
	{
		return Collections::wordStatus($status);
	}

	public function statusAvailableFilter($product)
	{
		return Collections::wordStatusAvailable($product);
	}

	public function paymentstatusFilter($status)
	{
		return Collections::wordPaymentStatus($status);
	}

	public function couponStatusText($coupon)
	{
		return Collections::couponStatusText($coupon);
	}

	public function getPriceData($rs)
	{
		return Products::getPriceData($rs);
	}

	public function getPercentProductDiscount($rs)
	{
		return Products::getPercentProductDiscount($rs);
	}

	public function getTrackingURL($tracking_url, $tracking_number)
	{
		return Collections::getTrackingURL($tracking_url, $tracking_number);
	}

	public function getName()
	{
		return 'app_extension';
	}

}
