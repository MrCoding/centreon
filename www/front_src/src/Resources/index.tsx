import * as React from 'react';

import { isNil } from 'ramda';

import { withSnackbar, ListingPage } from '@centreon/ui';

import WithPanel from '@centreon/ui/src/Panel/WithPanel';
import Context from './Context';
import Filter from './Filter';
import Listing from './Listing';
import Details from './Details';
import useFilter from './Filter/useFilter';
import useListing from './Listing/useListing';
import useActions from './Actions/useActions';
import useDetails from './Details/useDetails';
import EditFiltersPanel from './Filter/Edit';

const Resources = (): JSX.Element => {
  const listingContext = useListing();
  const filterContext = useFilter();
  const detailsContext = useDetails();
  const actionsContext = useActions();

  const { selectedDetailsEndpoints } = detailsContext;

  return (
    <Context.Provider
      value={{
        ...listingContext,
        ...filterContext,
        ...detailsContext,
        ...actionsContext,
      }}
    >
      <WithPanel
        panel={<EditFiltersPanel />}
        open={filterContext.editPanelOpen}
      >
        <ListingPage
          slidePanelOpen={!isNil(selectedDetailsEndpoints)}
          filters={<Filter />}
          listing={<Listing />}
          slidePanel={<Details />}
        />
      </WithPanel>
    </Context.Provider>
  );
};

export default withSnackbar(Resources);
