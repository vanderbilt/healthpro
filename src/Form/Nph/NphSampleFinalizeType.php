<?php

namespace App\Form\Nph;

use App\Entity\NphSample;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class NphSampleFinalizeType extends NphOrderForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sample = $options['sample'];
        $orderType = $options['orderType'];

        $this->addCollectedTimeAndNoteFields($builder, $options, $sample);

        if ($orderType === 'urine') {
            $this->addUrineMetadataFields($builder);
        }

        if ($orderType === 'stool') {
            $this->addStoolMetadataFields($builder);
        }

        $formData = $builder->getData();

        if (!empty($options['aliquots'])) {
            foreach ($options['aliquots'] as $aliquotCode => $aliquot) {
                $idData = $tsData = $volumeData = [];
                $aliquotCount = isset($formData[$aliquotCode]) ? count($formData[$aliquotCode]) : $aliquot['expectedAliquots'];
                for ($i = 0; $i < $aliquotCount; $i++) {
                    $idData[] = $formData[$aliquotCode][$i] ?? null;
                    $tsData[] = $formData["{$aliquotCode}AliquotTs"][$i] ?? null;
                    $volumeData[] = $formData["{$aliquotCode}Volume"][$i] ?? null;
                }
                $builder->add("{$aliquotCode}", Type\CollectionType::class, [
                    'entry_type' => Type\TextType::class,
                    'entry_options' => [
                        'constraints' => [
                            new Constraints\Type('string'),
                            new Constraints\Regex([
                                'pattern' => '/^[a-zA-Z0-9]{11}$/',
                                'message' => 'Aliquot barcode must be a string of 11 digits'
                            ]),
                            new Constraints\Callback(function ($value, $context) use ($aliquotCode) {
                                $formData = $context->getRoot()->getData();
                                $key = intval($context->getObject()->getName());
                                if (($formData["{$aliquotCode}AliquotTs"][$key] ||
                                        $formData["{$aliquotCode}Volume"][$key]) && empty($value)) {
                                    $context->buildViolation('Barcode is required')->addViolation();
                                }
                            })
                        ],
                        'attr' => [
                            'placeholder' => 'Scan Aliquot Barcode',
                            'class' => 'aliquot-barcode'
                        ],
                    ],
                    'label' => $aliquot['container'],
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'data' => $idData,
                ]);

                $builder->add("{$aliquotCode}AliquotTs", Type\CollectionType::class, [
                    'entry_type' => Type\DateTimeType::class,
                    'label' => false,
                    'entry_options' => [
                        'widget' => 'single_text',
                        'format' => 'M/d/yyyy h:mm a',
                        'html5' => false,
                        'view_timezone' => $options['timeZone'],
                        'model_timezone' => 'UTC',
                        'label' => false,
                        'constraints' => [
                            new Constraints\LessThanOrEqual([
                                'value' => new \DateTime('+5 minutes'),
                                'message' => 'Timestamp cannot be in the future'
                            ]),
                            new Constraints\Callback(function ($value, $context) use ($aliquotCode) {
                                $formData = $context->getRoot()->getData();
                                $key = intval($context->getObject()->getName());
                                if (($formData[$aliquotCode][$key] || $formData["{$aliquotCode}Volume"][$key])
                                    && empty($value)) {
                                    $context->buildViolation('Time is required')->addViolation();
                                }
                            })
                        ],
                        'attr' => [
                            'class' => 'order-ts',
                        ]
                    ],
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'data' => $tsData,
                ]);

                $builder->add("{$aliquotCode}Volume", Type\CollectionType::class, [
                    'entry_type' => Type\TextType::class,
                    'label' => 'Volume',
                    'entry_options' => [
                        'constraints' => [
                            new Constraints\Callback(function ($value, $context) use ($aliquotCode) {
                                $formData = $context->getRoot()->getData();
                                $key = intval($context->getObject()->getName());
                                if (($formData[$aliquotCode][$key] || $formData["{$aliquotCode}AliquotTs"][$key])
                                    && empty($value)) {
                                    $context->buildViolation('Volume is required')->addViolation();
                                }
                            })
                        ]
                    ],
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'data' => $volumeData,
                ]);
            }
        }

        $nphSample = $options['nphSample'];
        if ($nphSample->getModifyType() === NphSample::UNLOCK) {
            $finalizedAliquots = $nphSample->getNphAliquots();
            foreach ($finalizedAliquots as $finalizedAliquot) {
                $builder->add('cancel_' . $finalizedAliquot->getAliquotId(), Type\CheckboxType::class, [
                    'label' => false,
                    'required' => false,
                    'disabled' => $finalizedAliquot->getStatus() === NphSample::CANCEL
                ]);
                $builder->add('restore_' . $finalizedAliquot->getAliquotId(), Type\CheckboxType::class, [
                    'label' => false,
                    'required' => false,
                    'disabled' => $finalizedAliquot->getStatus() !== NphSample::CANCEL
                ]);
            }
        }

        // Placeholder field for displaying enter at least one aliquot message
        $builder->add('aliquotError', Type\CheckboxType::class, [
            'required' => false
        ]);

        return $builder->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'sample' => null,
            'orderType' => null,
            'timeZone' => null,
            'aliquots' => null,
            'disabled' => null,
            'nphSample' => null
        ]);
    }
}