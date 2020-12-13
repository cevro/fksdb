import { Price } from '@FKSDB/Model/Payment/price';
import PricePrinter from '@FKSDB/Model/ValuePrinters/PricePrinter';
import { translator } from '@translator/translator';
import * as React from 'react';

interface OwnProps {
    price: Price;
}

export default class PriceLabel extends React.Component<OwnProps, {}> {

    public render() {
        const {price} = this.props;
        return <small className="ml-3 price-label">
            {translator.getText('Price')}: <PricePrinter price={price}/>
        </small>;
    }
}
