import React, { Suspense } from 'react';

import { connect } from 'react-redux';
import { Switch, Route, withRouter } from 'react-router-dom';
import { isEmpty, equals } from 'ramda';

import { styled } from '@material-ui/core';

import internalPagesRoutes from '../../route-maps';
import { dynamicImport } from '../../helpers/dynamicImport';
import NotAllowedPage from '../../route-components/notAllowedPage';
import BreadcrumbTrail from '../../BreadcrumbTrail';
import { allowedPagesSelector } from '../../redux/selectors/navigation/allowedPages';

const PageContainer = styled('div')(({ theme }) => ({
  overflow: 'auto',
  height: '100%',
  display: 'grid',
  gridTemplateRows: 'auto 1fr',
  background: theme.palette.background.default,
}));

const getExternalPageRoutes = ({
  history,
  allowedPages,
  pages,
}): Array<JSX.Element> => {
  const basename = history.createHref({
    pathname: '/',
    search: '',
    hash: '',
  });

  const pageEntries = Object.entries(pages);
  const isAllowedPage = (path): boolean =>
    allowedPages.find((allowedPage) => path.includes(allowedPage));

  const loadablePages = pageEntries.filter(([path]) => isAllowedPage(path));

  return loadablePages.map(([path, parameter]) => {
    const Page = React.lazy(() => dynamicImport(basename, parameter));

    return (
      <Route
        key={path}
        path={path}
        exact
        render={(renderProps): JSX.Element => (
          <PageContainer>
            <BreadcrumbTrail path={path} />
            <Page {...renderProps} />
          </PageContainer>
        )}
      />
    );
  });
};

interface Props {
  allowedPages: Array<string>;
  history;
  pages: Record<string, unknown>;
  externalPagesFetched: boolean;
}

const ReactRouter = React.memo<Props>(
  ({ allowedPages, history, pages, externalPagesFetched }: Props) => {
    if (isEmpty(allowedPages)) {
      return null;
    }
    return (
      <Suspense fallback={null}>
        <Switch>
          {internalPagesRoutes.map(({ path, comp: Comp, ...rest }) => (
            <Route
              key={path}
              path={path}
              exact
              render={(renderProps): JSX.Element => (
                <PageContainer>
                  {allowedPages.includes(path) ? (
                    <>
                      <BreadcrumbTrail path={path} />
                      <Comp {...renderProps} />
                    </>
                  ) : (
                    <NotAllowedPage {...renderProps} />
                  )}
                </PageContainer>
              )}
              {...rest}
            />
          ))}
          {getExternalPageRoutes({ history, allowedPages, pages })}
          {externalPagesFetched && <Route component={NotAllowedPage} />}
        </Switch>
      </Suspense>
    );
  },
  (previousProps, nextProps) =>
    equals(previousProps.pages, nextProps.pages) &&
    equals(previousProps.allowedPages, nextProps.allowedPages) &&
    equals(previousProps.externalPagesFetched, nextProps.externalPagesFetched),
);

const mapStateToProps = (state): Record<string, unknown> => ({
  allowedPages: allowedPagesSelector(state),
  pages: state.externalComponents.pages,
  externalPagesFetched: state.externalComponents.fetched,
});

export default connect(mapStateToProps)(withRouter(ReactRouter));
