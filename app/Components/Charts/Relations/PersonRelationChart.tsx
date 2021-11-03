import {
    forceLink,
    forceManyBody,
    forceSimulation,
    forceX,
    forceY,
    SimulationLinkDatum,
    SimulationNodeDatum,
} from 'd3-force';
import * as React from 'react';

interface Data {
    created: string;
    gender: 'M' | 'F';
    personId: number;
}

interface OwnProps {
    data: {
        links: Array<{
            from: number;
            to: number;
            level: number | null;
        } & SimulationLinkDatum<SimulationNodeDatum>>;
        nodes: {
            [key: number]: {
                name: string;
                gender: 'M' | 'F';
            } & SimulationNodeDatum;
        };
    };
}

export default class PersonRelationChart extends React.Component<OwnProps, {}> {
    private simulation = null;

    public componentDidMount() {
        this.simulation.on('tick', () => {
            this.forceUpdate();
        });
    }

    public render() {
        const {data: {links, nodes}} = this.props;
        const simNodes = [];
        for (const key in nodes) {
            if (nodes.hasOwnProperty(key)) {
                simNodes.push(nodes[key]);
            }
        }
        links.forEach((link) => {
            link.source = nodes[link.from];
            link.target = nodes[link.to];
        });
        this.simulation = forceSimulation(simNodes)
            .force('link', forceLink(links))
            .force('charge', forceManyBody().strength(-150))
            .force('x', forceX())
            .force('y', forceY());

        const nodesElements = [];

        for (const key in nodes) {
            if (nodes.hasOwnProperty(key)) {
                const person = nodes[key];
                nodesElements.push(<g fill="currentColor" strokeLinecap="round"
                                      strokeLinejoin="round"
                                      transform={'translate(' + person.x + ',' + person.y + ')'}>
                    <circle stroke={person.gender === 'M' ? 'blue' : 'pink'} r="4"
                            strokeWidth="1.5"/>
                    <text x="8" y="0.31rem" fontSize=".5rem">
                        {person.name}
                    </text>
                </g>);
            }
        }

        return <div className="row">
            <div className="col-9">
                <svg viewBox="-600 -400 1200 800">
                    <g fill="none" strokeWidth="1.5">{links.map((item, index) => {
                        const r = Math.sqrt(Math.pow(item.target.x - item.source.x, 2) + Math.pow(item.target.y - item.source.y, 2)) / 2;
                        const rot = Math.atan((item.target.y - item.source.y) / (item.target.x - item.source.x)) * 180 / Math.PI;
                        return <path stroke={this.getColorByMeta(item.level)}
                                     strokeDasharray={(item.target.gender === item.source.gender) ? '5,5' : 'none'}
                                     d={
                                         'M ' + item.source.x + ' ' + item.source.y +
                                         ((item.level === 2 || item.level === 3) ? (' A ' + (r) + ' ' + r / 5 + ' ' + rot + ' 0 1 ' + item.target.x + ' ' + item.target.y) : '') +
                                         ' L ' + item.target.x + ' ' + item.target.y}/>;
                    })}</g>
                    <g>
                        {nodesElements}
                    </g>
                </svg>
            </div>
        </div>;
        // Per-type markers, as they don't inherit styles.
        /*     svg.append('defs')
                 .selectAll('marker')
                 .data(types)
                 .join('marker')
                 .attr('id', (d) => `arrow-${d}`)
                 .attr('viewBox', '0 -5 10 10')
                 .attr('refX', 15)
                 .attr('refY', -0.5)
                 .attr('markerWidth', 6)
                 .attr('markerHeight', 6)
                 .attr('orient', 'auto')
                 .append('path')
                 .attr('fill', color)
                 .attr('d', 'M0,-5L10,0L0,5');

             const node =



             node.append(

             simulation.on('tick', () => {
                 link.attr('d', linkArc);
                 node.attr('transform', (d) => `translate(${d.x},${d.y})`);
             });

             invalidation.then(() => simulation.stop());

             return svg.node();*/
        return null; /*<ChartContainer
            chart={LineChart}
            chartProps={{
                data: lineChartData,
                display: {xGrid: true, yGrid: true},
                xScale,
                yScale,
            }}
            legendComponent={LineChartLegend}
            legendProps={{data: lineChartData}}
        />;*/
    }

    private getColorByMeta(meta: number): string {
        switch (meta) {
            case 1:
                return 'green';
            case 2:
                return 'orange';
            case 3:
                return 'red';
            case 4:
                return 'violet';
            default:
                return '#ccc';
        }
    }
}
