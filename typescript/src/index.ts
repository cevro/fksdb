import { ajaxUpload } from '@apps/ajaxUpload';
import { charts } from '@apps/chart';
import { eventApplicationsTimeProgress } from '@apps/events/applicationsTimeProgress/';
import { attendance } from '@apps/events/attendance';
import { eventSchedule } from '@apps/events/schedule';
import { fyziklani } from '@apps/fyziklani/';
import { fyziklaniResults } from '@apps/fyziklaniResults';
import { person } from '@apps/person';
import { appsCollector } from '@appsCollector';

appsCollector.register(eventSchedule);
ajaxUpload();
eventApplicationsTimeProgress();
charts();
fyziklani();
fyziklaniResults();
attendance();
person();

appsCollector.run();
