import { lang } from '@i18n/i18n';
import * as React from 'react';
import Chart from './chart';

interface OwnProps {
    taskId: number;
    availablePoints: number[];
}

export default class Timeline extends React.Component<OwnProps, {}> {
    public constructor(props, context) {
        super(props, context);
        this.state = {from: props.gameStart, to: props.gameEnd};
    }

    public render() {
        const {taskId} = this.props;
        return (
            <div className={'fyziklani-chart-container'}>
                <h3>{lang.getText('Timeline')}</h3>
                <Chart taskId={taskId}/>
            </div>
        );
    }
}
