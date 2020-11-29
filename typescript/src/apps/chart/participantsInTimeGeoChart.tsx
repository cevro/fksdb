import ItemsPerCountryLog from '@apps/chart/itemsPerCountryLog';
import { GeoData } from '@shared/components/geoChart/geoData';
import * as React from 'react';

interface OwnProps {
    data: Array<{
        country: string;
        created: string;
    }>;
}

export default class ParticipantsInTimeGeoChart extends React.Component<OwnProps, { timestamp: number }> {

    public render() {
        const day = (1000 * 60 * 60 * 24);
        const {data} = this.props;
        let maxTimestamp = 0;
        let minTimestamp = (new Date()).getTime();

        const geoData: GeoData = {};

        data.forEach((datum) => {
            const time = (new Date(datum.created)).getTime();
            maxTimestamp = maxTimestamp > time ? maxTimestamp : time;
            minTimestamp = minTimestamp < time ? minTimestamp : time;
            if (!this.state || time < this.state.timestamp) {
                geoData[datum.country] = geoData[datum.country] || {count: 0};
                geoData[datum.country].count++;
            }
        });
        const value = this.state ? this.state.timestamp : maxTimestamp;
        return <div>
            <div className="form-group">
                <input type="range"
                       step={day}
                       className="form-control"
                       max={Math.ceil(maxTimestamp / day) * day}
                       min={Math.floor(minTimestamp / day) * day}
                       onChange={(event) => {
                           this.setState({timestamp: +event.target.value});
                       }}
                       value={value}/>
                <small className="form-text text-muted">{(new Date(value)).toISOString()}</small>
            </div>
            <ItemsPerCountryLog data={geoData}/>
        </div>;
    }
}
