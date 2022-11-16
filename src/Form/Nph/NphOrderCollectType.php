<?php

namespace App\Form\Nph;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class NphOrderCollectType extends AbstractType
{
    public static $urineColors = [
        'Color 1' => 1,
        'Color 2' => 2,
        'Color 3' => 3,
        'Color 4' => 4,
        'Color 5' => 5,
        'Color 6' => 6,
        'Color 7' => 7,
        'Color 8' => 8,
    ];

    public static $urineClarity = [
        'Clean' => 'clean',
        'Slightly Cloudy' => 'slightly_cloudy',
        'Cloudy' => 'cloudy',
        'Turbid' => 'turbid'
    ];

    public static $bowelMovements = [
        'I was constipated (had difficulty passing stool), and my stool looks like Type 1 and/or 2' => 'type12',
        'I had diarrhea (watery stool), and my stool looks like Type 5, 6, and/or 7' => 'type567',
        'I had normal formed stool, and my stool looks like Type 3 and/or 4' => 'type34'
    ];

    public static $bowelMovementQuality = [
        'I tend to be constipated (have difficulty passing stool) - Type 1 and 2' => 'qtype12',
        'I tend to have diarrhea (watery stool) - Type 5, 6, and 7' => 'qtype567',
        'I tend to have normal formed stool - Type 3 and 4' => 'qtype34'
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $samples = $options['samples'];
        $orderType = $options['orderType'];
        foreach ($samples as $sample => $sampleLabel) {
            $builder->add($sample, Type\CheckboxType::class, [
                'label' => $sampleLabel,
                'required' => false
            ]);
            $constraintDateTime = new \DateTime('+5 minutes'); // add buffer for time skew
            $builder->add("{$sample}CollectedTs", Type\DateTimeType::class, [
                'required' => false,
                'label' => 'Collection Time',
                'widget' => 'single_text',
                'format' => 'M/d/yyyy h:mm a',
                'html5' => false,
                'model_timezone' => 'UTC',
                'view_timezone' => $options['timeZone'],
                'constraints' => [
                    new Constraints\Type('datetime'),
                    new Constraints\LessThanOrEqual([
                        'value' => $constraintDateTime,
                        'message' => 'Date cannot be in the future'
                    ])
                ],
                'attr' => [
                    'class' => 'order-collected-ts',
                ]
            ]);
            $builder->add("{$sample}Notes", Type\TextareaType::class, [
                'label' => 'Notes',
                'required' => false,
                'constraints' => new Constraints\Type('string')
            ]);
        }

        if ($orderType === 'urine') {
            $builder->add('urineColor', Type\ChoiceType::class, [
                'label' => 'Urine Color',
                'required' => false,
                'choices' => self::$urineColors,
                'multiple' => false,
                'placeholder' => 'Select Urine Color'
            ]);

            $builder->add('urineClarity', Type\ChoiceType::class, [
                'label' => 'Urine Clarity',
                'required' => false,
                'choices' => self::$urineClarity,
                'multiple' => false,
                'placeholder' => 'Select Urine Clarity'
            ]);
        }

        if ($orderType === 'stool') {
            $builder->add('bowelMovement', Type\ChoiceType::class, [
                'label' => 'Describe the bowel movement for this collection',
                'required' => false,
                'choices' => self::$bowelMovements,
                'multiple' => false,
                'placeholder' => 'Select bowel movement type'
            ]);

            $builder->add('bowelMovementQuality', Type\ChoiceType::class, [
                'label' => 'Describe the typical quality of your bowel movements',
                'required' => false,
                'choices' => self::$bowelMovementQuality,
                'multiple' => false,
                'placeholder' => 'Select bowel movement quality'
            ]);
        }
        return $builder->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'samples' => null,
            'orderType' => null,
            'timeZone' => null
        ]);
    }
}
