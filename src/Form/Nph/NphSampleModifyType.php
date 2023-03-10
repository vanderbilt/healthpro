<?php

namespace App\Form\Nph;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\NphSample;

class NphSampleModifyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['type'] != NphSample::UNLOCK) {
            $samples = $options['samples'];
            foreach ($samples as $sample) {
                $disabled = false;
                $checked = false;
                if ($options['type'] === NphSample::CANCEL) {
                    $disabled = $sample->getModifyType() === NphSample::CANCEL;
                    $checked = true;
                } elseif ($options['type'] === NphSample::RESTORE) {
                    $disabled = $sample->getModifyType() !== NphSample::CANCEL;
                    $checked = true;
                    if (!$disabled) {
                        $disabled = in_array($sample->getSampleCode(), $options['activeSamples'], true);
                        $checked = false;
                    }
                }
                $builder->add($sample->getSampleCode(), Type\CheckboxType::class, [
                    'label' => false,
                    'required' => false,
                    'disabled' => $disabled,
                    'attr' => [
                        'checked' => $checked,
                    ]
                ]);
            }

            // Placeholder field for displaying select at least one sample error message
            $builder->add('samplesCheckAll', Type\CheckboxType::class, [
                'required' => false
            ]);
        }

        $reasonType = $options['type'] . 'Reasons';
        $reasons = NphSample::$$reasonType;
        $builder->add('reason', Type\ChoiceType::class, [
            'label' => 'Reason',
            'required' => true,
            'choices' => $reasons,
            'placeholder' => '-- Select ' . ucfirst($options['type']) . ' Reason --',
            'multiple' => false,
            'constraints' => new Constraints\NotBlank([
                'message' => "Please select {$options['type']} reason"
            ]),
            'attr' => ['class' => 'modify-reason']
        ]);
        $builder->add('otherText', Type\TextareaType::class, [
            'label' => false,
            'required' => false,
            'constraints' => [
                new Constraints\Type('string'),
                new Constraints\Callback(function ($value, $context) {
                    if (empty($value) && $context->getRoot()['reason']->getData() === 'OTHER') {
                        $context->buildViolation('Please enter a reason')->addViolation();
                    }
                })
            ],
            'attr' => ['class' => 'modify-other-text']
        ]);
        if ($options['type'] == NphSample::CANCEL) {
            $builder->add('confirm', Type\TextType::class, [
                'label' => 'Confirm',
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type('string'),
                    new Constraints\Callback(function ($value, $context) {
                        if (strtolower($value) !== NphSample::CANCEL) {
                            $context->buildViolation('Please type the word "CANCEL" to confirm')->addViolation();
                        }
                    })
                ],
                'attr' => [
                    'placeholder' => 'Type the word "CANCEL" to confirm',
                    'autocomplete' => 'off'
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => null,
            'samples' => null,
            'activeSamples' => null,
        ]);
    }
}
