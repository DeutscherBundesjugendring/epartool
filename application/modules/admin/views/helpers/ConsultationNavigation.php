<?php

class Admin_View_Helper_ConsultationNavigation extends Zend_View_Helper_Abstract
{
    /**
     * Returns consultation secondary navigation html
     * @param  integer $consultation   The consultation array
     * @param  string  $activeItem     Name of the active item
     * @return string                  The menu html
     */
    public function consultationNavigation($consultation, $activeItem = null)
    {
        $items = array(
            array(
                'name' => 'dashboard',
                'label' => $this->view->translate('Dashboard'),
                'href' => $this->view->url(
                    ['controller' => 'consultation', 'action' => 'index', 'kid' => $consultation['kid']]
                ),
            ),
            array(
                'name' => 'info',
                'label' => 'Info',
                'href' => $this->view->url(
                    ['controller' => 'article', 'action' => 'index', 'kid' => $consultation['kid']]
                ),
                'new_item' => $this->view->url(
                    ['controller' => 'article', 'action' => 'create', 'kid' => $consultation['kid']]
                ),
            ),
            array(
                'name' => 'questions',
                'label' => $this->view->translate('Questions'),
                'href' => $this->view->url(
                    ['controller' => 'question', 'action' => 'index', 'kid' => $consultation['kid']]
                ),
                'new_item' => $this->view->url(
                    ['controller' => 'question', 'action' => 'create', 'kid' => $consultation['kid']]
                ),
            )
        );

        if ($consultation['is_input_phase_showed']) {
            $item = [
                'name' => 'contributions',
                'label' => $this->view->translate('Contributions'),
                'href' => $this->view->url(
                    ['controller' => 'input', 'action' => 'index', 'kid' => $consultation['kid']]
                ),
            ];

            if ((new Model_Questions())->getCountByConsultation($consultation['kid']) > 0) {
                $item['new_item'] = $this->view->url(
                    ['controller' => 'input', 'action' => 'create', 'kid' => $consultation['kid']]
                );
            }
            $items[] = $item;
        }

        if ($consultation['is_voting_phase_showed']) {
            $items[] = [
                'name' => 'voting-prepare',
                'label' => $this->view->translate('Voting'),
                'href' => $this->view->url(
                    ['controller' => 'votingprepare', 'action' => 'index', 'kid' => $consultation['kid']]
                ),
                'children' => [
                    array(
                        'name' => 'voting-prepare',
                        'label' => $this->view->translate('Preparation'),
                        'href' => $this->view->url(
                            ['controller' => 'votingprepare', 'action' => 'index', 'kid' => $consultation['kid']]
                        ),
                    ),
                    array(
                        'name' => 'voting-permissions',
                        'label' => $this->view->translate('Permissions'),
                        'href' => $this->view->url(
                            ['controller' => 'voting', 'action' => 'index', 'kid' => $consultation['kid']]
                        ),
                    ),
                    array(
                        'name' => 'voting-participants',
                        'label' => $this->view->translate('Participants'),
                        'href' => $this->view->url(
                            ['controller' => 'voting', 'action' => 'participants', 'kid' => $consultation['kid']]
                        ),
                    ),
                    array(
                        'name' => 'voting-results',
                        'label' => $this->view->translate('Results'),
                        'href' => $this->view->url(
                            ['controller' => 'voting', 'action' => 'results', 'kid' => $consultation['kid']]
                        ),
                    ),
                ]
            ];
        }

        if ($consultation['is_followup_phase_showed']) {
            $items[] = [
                'name' => 'followup',
                'label' => $this->view->translate('Reaction & Impact'),
                'href' => $this->view->url(
                    ['controller' => 'followup', 'action' => 'index', 'kid' => $consultation['kid']]
                ),
                'new_item' => $this->view->url(
                    ['controller' => 'followup', 'action' => 'create-followup', 'kid' => $consultation['kid']]
                ),
            ];
        }

        $items[] = [
            'name' => 'stats',
            'label' => $this->view->translate('Statistics'),
            'href' => $this->view->url(
                ['controller' => 'consultation', 'action' => 'report', 'kid' => $consultation['kid']]
            ),
        ];

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
