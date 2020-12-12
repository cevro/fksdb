import {
    scaleLinear,
    scaleTime,
} from 'd3-scale';
import * as React from 'react';
import { translator } from '@translator/Translator';
import { LineChartData, PointData } from '@FKSDB/Components/Controls/Chart/LineChart/Middleware';
import LineChartComponent from '@FKSDB/Components/Controls/Chart/LineChart/LineChartComponent';
import LegendComponent from '@FKSDB/Components/Controls/Chart/LineChart/LegendComponent';

interface Data {
    created: string;
    gender: 'M' | 'F';
    personId: number;
}

interface OwnProps {
    data: Data[];
}

export default class TotalPersonsChartComponent extends React.Component<OwnProps, {}> {

    public render() {
        const {data} = this.props;
        const lineChartData: LineChartData = [];
        const pointsAll: PointData[] = [];
        const pointsMale: PointData[] = [];
        const pointsFemale: PointData[] = [];
        const pointsPersonId: PointData[] = [];

        let maleIndex = 0;
        let femaleIndex = 0;
        data.forEach((person, index) => {
            const date = new Date(person.created);
            pointsAll.push({
                xValue: date,
                yValue: index,
            });
            pointsPersonId.push({
                xValue: date,
                yValue: person.personId,
            });
            if (person.gender === 'M') {
                maleIndex++;
                pointsMale.push({
                    xValue: date,
                    yValue: maleIndex,
                });
            } else {
                femaleIndex++;
                pointsFemale.push({
                    xValue: date,
                    yValue: femaleIndex,
                });
            }
        });
        const display = {
            area: false,
            lines: true,
            points: false,
        };
        lineChartData.push({
            color: 'gray',
            display,
            name: translator.getText('All'),
            points: pointsAll,
        });

        lineChartData.push({
            color: '#1175da',
            display,
            name: translator.getText('Male'),
            points: pointsMale,
        });

        lineChartData.push({
            color: '#da1175',
            display,
            name: translator.getText('Female'),
            points: pointsFemale,
        });
        lineChartData.push({
            color: '#da7511',
            display,
            name: translator.getText('Person Id'),
            points: pointsPersonId,
        });

        const yScale = scaleLinear<number, number>().domain([0, data[data.length - 1].personId]);
        const xScale = scaleTime().domain([new Date(data[0].created), new Date()]);

        return <div className="row">
            <div className="chart-container col-lg-9 col-md-8">
                <LineChartComponent data={lineChartData} xScale={xScale} yScale={yScale} display={{xGrid: true, yGrid: true}}/>
            </div>
            <div className="chart-legend-container col-lg-3 col-md-4">
                <LegendComponent data={lineChartData}/>
            </div>
        </div>;
    }
}
