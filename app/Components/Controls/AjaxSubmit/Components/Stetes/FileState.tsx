import { Store } from '@FKSDB/Components/Controls/AjaxSubmit/Reducers';
import { dispatchFetch } from '@FKSDB/Model/FrontEnd/Fetch/netteFetch';
import { NetteActions } from '@FKSDB/Model/FrontEnd/Loader/netteActions';
import { translator } from '@translator/translator';
import * as React from 'react';
import { connect } from 'react-redux';
import {
    Action,
    Dispatch,
} from 'redux';
import { Submit } from '../../middleware';

interface OwnProps {
    submit: Submit;
}

interface DispatchProps {
    onDeleteFile(url: string): void;
}

interface StateProps {
    actions: NetteActions;
}

class FileState extends React.Component<OwnProps & DispatchProps & StateProps, {}> {

    public render() {
        return <div className="uploaded-file">
            <button aria-hidden="true" className="pull-right btn btn-warning" title={translator.getText('Revoke')}
                    onClick={() => {
                        if (window.confirm(translator.getText('Remove submit?'))) {
                            this.props.onDeleteFile(this.props.actions.getAction('revoke'));
                        }
                    }}>&times;</button>
            <div className="text-center p-2">
                <a href={this.props.actions.getAction('download')}>
                    <span className="display-1 w-100"><i className="fa fa-file-pdf-o"/></span>
                    <span className="d-block">{this.props.submit.name}</span>
                </a>
            </div>
        </div>;
    }
}

const mapDispatchToProps = (dispatch: Dispatch<Action<string>>): DispatchProps => {
    return {
        onDeleteFile: (url: string) => dispatchFetch<Submit>(url, dispatch, JSON.stringify({})),
    };
};
const mapStateToProps = (state: Store): StateProps => {
    return {
        actions: state.fetchApi.actions,
    };
};
export default connect(mapStateToProps, mapDispatchToProps)(FileState);
