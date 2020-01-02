/* eslint-disable import/no-extraneous-dependencies */

import { createStore, applyMiddleware, compose } from 'redux';
import { routerMiddleware } from 'connected-react-router';
import { batchDispatchMiddleware } from 'redux-batched-actions';
import thunk from 'redux-thunk';
import { createBrowserHistory } from 'history';
import createRootReducer from '../redux/reducers/index.ts';

const paths = window.location.pathname.split('/');
export const history = createBrowserHistory({
  basename: `/${paths[1] ? paths[1] : ''}`,
});

const createAppStore = (initialState: object = {}): object => {
  const middlewares = [
    routerMiddleware(history),
    thunk,
    batchDispatchMiddleware,
  ];

  const store = createStore(
    createRootReducer(history),
    initialState,
    compose(
      applyMiddleware(...middlewares),
      window.devToolsExtension ? window.devToolsExtension() : (f) => f,
    ),
  );
  return store;
};

export default createAppStore;
