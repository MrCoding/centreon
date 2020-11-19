import * as React from 'react';

import { ScaleTime } from 'd3-scale';

import { TimelineEvent } from '../../../../Details/tabs/Timeline/models';
import CommentAnnotations from './Comments';

export interface Props {
  xScale: ScaleTime<number, number>;
  timeline: Array<TimelineEvent>;
  graphHeight: number;
}

const Annotations = ({ xScale, timeline, graphHeight }: Props): JSX.Element => {
  return (
    <CommentAnnotations
      xScale={xScale}
      timeline={timeline}
      graphHeight={graphHeight}
    />
  );
};

export default Annotations;
