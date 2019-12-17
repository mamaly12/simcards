<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class ServerForm
 *
 * @category SimCard
 *
 * @package App\Form
 * @author  MohammadAghaAbbasloo <a.mohammad85@gmail.com>
 * @license Copyright (c) 2019, CKSource - All rights reserved.
 * @link    localhost
 */

class TopUpForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a number',
                    ]),
                ],
            ])

            ->add('amount', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a balance',
                    ]),
                ],
            ])

            ->add('currency', ChoiceType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a currency',
                    ]),
                    new Choice([
                        'choices' => ['USD', 'EURO'],
                        'message' => 'Choose a valid currency.',
                    ]),
                ],
                'choices' => [
                    'USD' => 'USD',
                    'EURO' => 'EURO',
                ],
            ])

            ->add('save', SubmitType::class, array('label'=>'add','attr'=>array('class'=>'btn btn-primary mt-3')))
        ;
    }

}
