import { Price } from '@FKSDB/Model/Payment/price';
import { translator } from '@translator/translator';
import * as React from 'react';

interface OwnProps {
    price: Price;
}

export default class PricePrinter extends React.Component<OwnProps, {}> {

    public render() {
        const {price: {eur, czk}} = this.props;
        if (+eur === 0 && +czk === 0) {
            return <span>{translator.getText('for free')}</span>;
        }
        if (+eur === 0) {
            return <span>{czk} Kč</span>;
        }
        return <span>{eur} €/{czk} Kč</span>;
    }
}
