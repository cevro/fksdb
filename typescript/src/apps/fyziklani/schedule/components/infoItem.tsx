import { lang } from '@i18n/i18n';
import * as React from 'react';
import { LocalizedInfo } from './index';
import Item from './item';

interface OwnProps {
    item: LocalizedInfo;
    blockName: string;
}

export default class InfoItem extends React.Component<OwnProps, {}> {

    public render() {
        const {item} = this.props;
        const langKey = lang.getCurrentLocale();
        if (!item.hasOwnProperty(langKey)) {
            return null;
        }
        const localizedData = item[langKey];
        return <div>
            <Item className={'info-container text-muted'} icon={<span className={'fa fa-info w-100'}/>}>
                <div>
                    <span className={'font-weight-bold'}>{localizedData.name}</span>
                </div>
                <div className={'font-italic'}>{localizedData.description}</div>
            </Item>
        </div>;
    }
}
