import { lang } from '@i18n/i18n';
import * as React from 'react';
import Legend from '../legend';
import Chart from './chart';

interface OwnProps {
    teamId: number;
}

export default class PointsInTime extends React.Component<OwnProps, {}> {

    public render() {
        const {teamId} = this.props;
        return (
            <div className={'fyziklani-chart-container'}>
                <h3>{lang.getText('Time progress')}</h3>
                <div className="row">
                    <Chart teamId={teamId}/>
                    <Legend inline={false}/>
                </div>
            </div>
        );
    }
}
