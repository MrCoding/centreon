import * as React from 'react';

import clsx from 'clsx';

import {
  Typography,
  makeStyles,
  useTheme,
  fade,
  Theme,
} from '@material-ui/core';
import { CreateCSSProperties } from '@material-ui/styles';

import { useResourceContext } from '../../Context';

import { Line } from './models';

const useStyles = makeStyles<Theme, { panelWidth: number }>((theme) => ({
  item: {
    display: 'flex',
    alignItems: 'center',
    margin: theme.spacing(0, 1, 1, 0),
  },
  icon: {
    width: 9,
    height: 9,
    borderRadius: '50%',
    marginRight: theme.spacing(1),
  },
  caption: ({ panelWidth }) => ({
    marginRight: theme.spacing(1),
    color: fade(theme.palette.common.black, 0.6),
    overflow: 'hidden',
    textOverflow: 'ellipsis',
    maxWidth: 0.85 * panelWidth,
  }),
  hidden: {
    color: theme.palette.text.disabled,
  },
  toggable: {
    cursor: 'pointer',
    '&:hover': {
      color: theme.palette.common.black,
    },
  },
}));

interface Props {
  lines: Array<Line>;
  toggable: boolean;
  onItemToggle: (metric: string) => void;
  onItemHighlight: (metric: string) => void;
  onItemSelect: (metric: string) => void;
  onClearItemHighlight: () => void;
}

const Legend = ({
  lines,
  onItemToggle,
  onItemSelect,
  toggable,
  onItemHighlight,
  onClearItemHighlight,
}: Props): JSX.Element => {
  const { panelWidth } = useResourceContext();
  const classes = useStyles({ panelWidth });
  const theme = useTheme();

  const getLegendName = ({ metric, name, display }: Line): JSX.Element => {
    return (
      <div
        onMouseEnter={(): void => onItemHighlight(metric)}
        onMouseLeave={(): void => onClearItemHighlight()}
      >
        <Typography
          className={clsx(
            {
              [classes.hidden]: !display,
              [classes.toggable]: toggable,
            },
            classes.caption,
          )}
          variant="body2"
          onClick={(event: React.MouseEvent): void => {
            if (!toggable) {
              return;
            }

            if (event.ctrlKey) {
              onItemToggle(metric);
              return;
            }

            onItemSelect(metric);
          }}
        >
          {name}
        </Typography>
      </div>
    );
  };

  return (
    <>
      {lines.map((line) => {
        const { color, name, display } = line;

        const iconBackgroundColor = display
          ? color
          : fade(theme.palette.text.disabled, 0.2);

        return (
          <div className={classes.item} key={name}>
            <div
              className={clsx(classes.icon, { [classes.hidden]: !display })}
              style={{ backgroundColor: iconBackgroundColor }}
            />
            {getLegendName(line)}
          </div>
        );
      })}
    </>
  );
};

export default Legend;
