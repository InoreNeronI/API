<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

/**
 * Description of UserDataPersister
 *
 * @author daniel
 */
class UserDataPersister implements DataPersisterInterface
{

	private $entityManager;
	private $userPasswordEncoder;


	public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder) 
	{

		$this->entityManager = $entityManager;
		$this->userPasswordEncoder = $userPasswordEncoder;

	}
	
	public function supports($data) : bool
	{
		//method
		
		return $data instanceof User;
	}
	
	
	/**
	 * 
	 * @param User $data
	 */
	public function persist($data)
	{
		//method
		
		if ($data->getPlainPassword()) 
		{
			
			$data->setPassword(
				$this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
			);
			$data->eraseCredentials();
			
		}
		
		$this->entityManager->persist($data);
		$this->entityManager->flush();
		
	}
	
	public function remove($data) 
	{
		//method
		
		$this->entityManager->remove($data);
		$this->entityManager->flush();
		
	}
}
