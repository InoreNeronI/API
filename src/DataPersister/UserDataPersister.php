<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Users;


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
		
		return $data instanceof Users;
	}
	
	
	/**
	 * 
	 * @param Users $data
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
