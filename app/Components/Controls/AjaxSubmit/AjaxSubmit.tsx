import ActionsStoreCreator from '@FKSDB/Model/FrontEnd/Fetch/ActionsStoreCreator';
import { NetteActions } from '@FKSDB/Model/FrontEnd/Loader/netteActions';
import { ModelSubmit } from '@FKSDB/Model/ORM/Models/modelSubmit';
import * as React from 'react';
import UploadContainer from './Components/Container';
import { app } from './Reducers';
import './style.scss';

interface IProps {
    data: ModelSubmit;
    actions: NetteActions;
}

export default class AjaxSubmit extends React.Component<IProps, {}> {

    public render() {
        return <ActionsStoreCreator
            storeMap={{
                actions: this.props.actions,
                data: this.props.data,
                messages: [],
            }}
            app={app}
        >
            <UploadContainer/>
        </ActionsStoreCreator>;
    }
}
