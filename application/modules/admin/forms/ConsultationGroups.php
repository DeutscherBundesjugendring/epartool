<?php

class Admin_Form_ConsultationGroups extends Dbjr_Form_Admin
{

    /**
     * @var Zend_Db_Table_Row
     */
    private $consultation;

    /**
     * Admin_Form_ConsultationGroups constructor.
     * @param Zend_Db_Table_Row $consultation
     * @param null $options
     */
    public function __construct($consultation, $options = null)
    {
        $this->consultation = $consultation;
        parent::__construct($options);
    }

    public function init()
    {
        $this->setIsArray(true);

        $activateInfinity = $this->createElement('checkbox', 'activateToInfinityAge');
        $activateInfinity
            ->setLabel('Activate')
            ->setDecorators(
                [
                    'ViewHelper',
                    'Description',
                    'labelText',
                    [
                        'label' => 'HtmlTag',
                        ['tag' => 'label']
                    ],
                    [
                        ['wrapper' => 'HtmlTag'],
                        ['tag' => 'div', 'class' => 'checkbox offset-bottom-none']
                    ]
                ]
            );
        $this->addElement($activateInfinity);

        $toInfinityAgeFrom = $this->createElement('text', 'toInfinityAgeFrom');
        $toInfinityAgeFrom
            ->setLabel('From')
            ->addValidator('Int')
            ->setAttrib('class', 'form-control')
            ->setDecorators(
                [
                    'ViewHelper',
                    [
                        'HtmlTag',
                        ['tag' => 'div', 'class' => 'form-group offset-bottom-none']
                    ],
                    [
                        'Label',
                        ['class' => 'sr-only']
                    ]
                ]
            );
        $this->addElement($toInfinityAgeFrom);

        $activateNoInformationValue = $this->createElement('checkbox', 'activateNoInformationValue');
        $activateNoInformationValue
            ->setLabel('Activate “No Information” value');
        $this->addElement($activateNoInformationValue);

        $contributorAges = (new Model_ContributorAge())->fetchAll([
            'consultation_id = ?' => $this->consultation['kid'],
        ]);
        $contributorAgesForm = new Dbjr_Form_SubFormAdmin();
        $contributorAgesForm->setIsArray(true);
        foreach ($contributorAges as $contributorAge) {
            if ($contributorAge['from'] > 0 && $contributorAge['to'] === null) {
                continue;
            }
            $subForm = new Dbjr_Form_SubFormAdmin();
            $subForm->setIsArray(true);

            $from = $subForm->createElement('text', 'from', [
                'belongsTo' => 'contributorAges[' . $contributorAge['id'] . ']',
            ]);
            $from
                ->setLabel('From')
                ->addValidator('Int')
                ->setAttrib('class', 'form-control')
                ->setDecorators(
                    [
                        'ViewHelper',
                        [
                            'HtmlTag',
                            ['tag' => 'div', 'class' => 'form-group offset-bottom-none']
                        ],
                        [
                            'Label',
                            ['class' => 'sr-only']
                        ]
                    ]
                );
            $subForm->addElement($from, 'from');

            $to = $subForm->createElement('text', 'to', [
                'belongsTo' => 'contributorAges[' . $contributorAge['id'] . ']',
            ]);
            $to
                ->setLabel('To')
                ->addValidator('Int')
                ->setAttrib('class', 'form-control')
                ->setDecorators(
                    [
                        'ViewHelper',
                        [
                            'HtmlTag',
                            ['tag' => 'div', 'class' => 'form-group offset-bottom-none']
                        ],
                        [
                            'Label',
                            ['class' => 'sr-only']
                        ]
                    ]
                );
            $subForm->addElement($to, 'to');

            $contributorAgesForm->addSubForm($subForm, $contributorAge['id']);
        }
        $this->addSubForm($contributorAgesForm, 'contributorAges');


        $singleFrom = $this->createElement('text', 'singleFrom');
        $singleFrom
            ->setLabel('From')
            ->setAttribs(['disabled' => 'disabled', 'class' => 'form-control'])
            ->setValue(1)
            ->setDecorators(
                [
                    'ViewHelper',
                    [
                        'HtmlTag',
                        ['tag' => 'div', 'class' => 'form-group offset-bottom-none']
                    ],
                    [
                        'Label',
                        ['class' => 'sr-only']
                    ]
                ]
            );
        $this->addElement($singleFrom);
        $singleTo = $this->createElement('text', 'singleTo');
        $singleTo
            ->setLabel('To')
            ->setAttribs(['disabled' => 'disabled', 'class' => 'form-control'])
            ->setValue(2)
            ->setDecorators(
                [
                    'ViewHelper',
                    [
                        'HtmlTag',
                        ['tag' => 'div', 'class' => 'form-group offset-bottom-none']
                    ],
                    [
                        'Label',
                        ['class' => 'sr-only']
                    ]
                ]
            );
        $this->addElement($singleTo);

        $activateInfinity = $this->createElement('checkbox', 'activateToInfinitySize');
        $activateInfinity
            ->setLabel('Activate')
            ->setDecorators(
                [
                    'ViewHelper',
                    'Description',
                    'labelText',
                    [
                        'label' => 'HtmlTag',
                        ['tag' => 'label']
                    ],
                    [
                        ['wrapper' => 'HtmlTag'],
                        ['tag' => 'div', 'class' => 'checkbox offset-bottom-none']
                    ]
                ]
            );
        $this->addElement($activateInfinity);

        $toInfinitySizeFrom = $this->createElement('text', 'toInfinitySizeFrom');
        $toInfinitySizeFrom
            ->setLabel('From')
            ->addValidator('Int')
            ->setAttrib('class', 'form-control')
            ->setDecorators(
                [
                    'ViewHelper',
                    [
                        'HtmlTag',
                        ['tag' => 'div', 'class' => 'form-group offset-bottom-none']
                    ],
                    [
                        'Label',
                        ['class' => 'sr-only']
                    ]
                ]
            );
        $this->addElement($toInfinitySizeFrom);

        $groupSizes = (new Model_GroupSize())->fetchAll(['consultation_id = ?' => $this->consultation['kid']]);
        $groupSizesForm = new Dbjr_Form_SubFormAdmin();
        $groupSizesForm->setIsArray(true);
        foreach ($groupSizes as $groupsSize) {
            if (((int) $groupsSize['from'] === 1 && (int) $groupsSize['to'] === 2)
                || ($groupsSize['from'] > 0 && $groupsSize['to'] === null)) {
                continue;
            }
            $subForm = new Dbjr_Form_SubFormAdmin();
            $subForm->setIsArray(true);

            $from = $subForm->createElement('text', 'from', [
                'belongsTo' => 'groupSizes[' . $groupsSize['id'] . ']',
            ]);
            $from
                ->setLabel('From')
                ->addValidator('Int')
                ->setAttrib('class', 'form-control')
                ->setDecorators(
                    [
                        'ViewHelper',
                        [
                            'HtmlTag',
                            ['tag' => 'div', 'class' => 'form-group offset-bottom-none']
                        ],
                        [
                            'Label',
                            ['class' => 'sr-only']
                        ]
                    ]
                );
            $subForm->addElement($from, 'from');

            $to = $subForm->createElement('text', 'to', [
                'belongsTo' => 'groupSizes[' . $groupsSize['id'] . ']',
            ]);
            $to
                ->setLabel('To')
                ->addValidator('Int')
                ->setAttrib('class', 'form-control')
                ->setDecorators(
                    [
                        'ViewHelper',
                        [
                            'HtmlTag',
                            ['tag' => 'div', 'class' => 'form-group offset-bottom-none']
                        ],
                        [
                            'Label',
                            ['class' => 'sr-only']
                        ]
                    ]
                );
            $subForm->addElement($to, 'to');

            $groupSizesForm->addSubForm($subForm, $groupsSize['id']);
        }
        $this->addSubForm($groupSizesForm, 'groupSizes');

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Save');
        $this->addElement($submit);
    }
}
