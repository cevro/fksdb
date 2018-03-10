<?php

class DispatchPresenter extends AuthenticatedPresenter {

    use \LanguageNav;

    public function renderDefault() {
        /**
         * @var $login ModelLogin
         */
        $login = $this->getPresenter()->getUser()->getIdentity();
        $query = $this->serviceContest->where('1=1');
        $result = [];
        foreach ($query as $row) {

            /**
             * @var $contest ModelContest
             * @var $row ModelContest
             */
            $contest = $this->serviceContest->findByPrimary($row->contest_id);
            $symbol = $contest->getContestSymbol();
            $allowed = [];
            foreach ([ModelRole::ORG, ModelRole::CONTESTANT] as $role) {
                $allowed[$role] = $this->check($login, $contest, $role);;
            }
            $result[$symbol] = ['allowed' => $allowed, 'contest' => $contest];
        }
        $this->template->contests = $result;
    }

    private function check(ModelLogin $login, ModelContest $contest, $role) {
        switch ($role) {
            case ModelRole::ORG:
                foreach ($login->getActiveOrgsContests($this->yearCalculator) as $contestId => $org) {
                    if ($contest->contest_id == $contestId) {
                        return true;
                    }
                };
                return false;
            default:
            case ModelRole::CONTESTANT:
                $person = $login->getPerson();
                if ($person) {
                    foreach ($person->getActiveContestants($this->yearCalculator) as $contestId => $org) {
                        if ($contest->contest_id == $contestId) {
                            return true;
                        }
                    }
                }
                return false;
        }
    }

    public function handleChangeContest($contestId, $role) {
        switch ($role) {
            case 'org':
                $this->redirect(':Org:Dashboard:default', [
                    'contestId' => $contestId,
                ]);
                return;
            case 'contestant':
                $this->redirect(':Public:Dashboard:default', [
                    'contestId' => $contestId,
                ]);
                return;
        }
    }

    public function getTitle() {
        return _('Razcestník');
    }

    public function getNavBarVariant() {
        return ['dark', 'dark'];
    }
}
