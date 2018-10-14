import * as React from 'react';
import {
    IInputConnectorItems,
} from '../reducers';

export interface ICoreProps {
    input: HTMLInputElement;
}

interface IState {
    onSetInitialData?: (value: IInputConnectorItems) => void;
    data?: IInputConnectorItems;
}

export default class CoreConnector<TState> extends React.Component<ICoreProps & IState, {}> {

    public componentDidMount() {
        const {input, onSetInitialData} = this.props;
        if (input.value) {
            onSetInitialData(JSON.parse(input.value));
        }
    }

    public componentWillReceiveProps(newProps: ICoreProps & IState) {
        const data: IInputConnectorItems = {}; // FIXME
        let hasValue = false;

        for (const key in newProps.data) {
            if (newProps.data.hasOwnProperty(key) && (newProps.data[key] !== null)) {
                data[key] = newProps.data[key];
                hasValue = true;
            }
        }
        this.props.input.value = hasValue ? JSON.stringify(data) : null;
    }

    public render() {
        return null;
    }
}
