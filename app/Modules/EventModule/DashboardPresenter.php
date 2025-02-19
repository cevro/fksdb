<?php

declare(strict_types=1);

namespace FKSDB\Modules\EventModule;

use FKSDB\Models\Events\Exceptions\EventNotFoundException;
use FKSDB\Models\UI\PageTitle;

class DashboardPresenter extends BasePresenter
{

    /**
     * @throws EventNotFoundException
     */
    public function titleDefault(): PageTitle
    {
        return new PageTitle(\sprintf(_('Event %s'), $this->getEvent()->name), 'fa fa-calendar-alt');
    }

    /**
     * @throws EventNotFoundException
     */
    public function authorizedDefault(): void
    {
        $this->setAuthorized($this->isEventOrContestOrgAuthorized('event.dashboard', 'default'));
    }

    /**
     * @throws EventNotFoundException
     */
    final public function renderDefault(): void
    {
        $this->template->event = $this->getEvent();
        $this->template->webUrl = $this->getWebUrl();
    }

    /**
     * @throws EventNotFoundException
     */
    private function getWebUrl(): string
    {
        switch ($this->getEvent()->event_type_id) {
            case 1:
                // FOF
                return 'http://fyziklani.cz/';
            case 2:
                // DSEF
                return \sprintf('https://fykos.cz/rocnik%02d/dsef/', $this->getEvent()->year);
            case 3:
                // VAF
                return \sprintf('https://fykos.cz/rocnik%02d/vaf/', $this->getEvent()->year);
            case 4:
                // sous-jaro
                return \sprintf('https://fykos.cz/rocnik%02d/sous-jaro/', $this->getEvent()->year);
            case 5:
                // sous-podzim
                return \sprintf('https://fykos.cz/rocnik%02d/sous-podzim/', $this->getEvent()->year);
            case 6:
                // cern
                return \sprintf('https://fykos.cz/rocnik%02d/cern/', $this->getEvent()->year);
            case 7:
                // TSAF
                return \sprintf('https://fykos.cz/rocnik%02d/tsaf/', $this->getEvent()->year);
            case 8:
                // MFnáboj
                return '#'; // FIXME
            case 9:
                // FOL
                return 'https://online.fyziklani.cz';
            // 1 Fyziklání online
            case 10:
                // Tábor výfuku
                return \sprintf('http://vyfuk.mff.cuni.cz/akce/tabor/tabor%d', $this->getEvent()->begin->format('Y'));
            case 11:
                // setkani jaro
                return \sprintf('http://vyfuk.mff.cuni.cz/akce/setkani/jaro%d', $this->getEvent()->begin->format('Y'));
            case 12:
                // setkani podzim
                return \sprintf(
                    'http://vyfuk.mff.cuni.cz/akce/setkani/podzim%d',
                    $this->getEvent()->begin->format('Y')
                );
            case 13:
                // Náboj Junior
                return \sprintf('#'); // FIXME
            case 14:
                //DSEF 2
                return \sprintf('https://fykos.cz/rocnik%02d/dsef2/', $this->getEvent()->year);
            default:
                return '#';
        }
    }
}
