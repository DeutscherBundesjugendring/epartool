import React from 'react';
import FollowUpTimeline from '../FollowUpTimeline/FollowUpTimeline';
import resolveElement from '../../service/resolveElement';
import {
  fetchFollowUpElement,
  fetchFollowUpElementParents,
  fetchFollowUpElementChildren,
} from '../../actions';


class FollowUpContainer extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      columns: [[]],
      hasError: false,
    };
  }

  componentDidMount() {
    const { followUpType, followUpId } = this.props;

    fetchFollowUpElement(followUpType, followUpId)
      .then((response) => {
        const resolvedElement = resolveElement(
          response,
          () => this.getParents(followUpType, followUpId),
          () => this.getChildren(followUpType, followUpId)
        );

        this.setState({ columns: [[resolvedElement]] });
      })
      .catch(() => this.setState({ hasError: true }));
  }

  getElementColumnIndex(followUpType, followUpId) {
    let columnIndex = null;

    this.state.columns.forEach((column, index) => {
      column.forEach((element) => {
        if (followUpType === element.props.type && followUpId === element.props.id) {
          columnIndex = index;
        }
      });
    });

    return columnIndex;
  }

  getParents(followUpType, followUpId) {
    const columnIndex = this.getElementColumnIndex(followUpType, followUpId);
    let clearParentalColumns = true;

    fetchFollowUpElementParents(followUpType, followUpId)
      .then((multipleResponse) => {
        multipleResponse.forEach((response) => {
          const resolvedElement = resolveElement(
            response,
            () => this.getParents(response.type, parseInt(response.id, 10)),
            () => this.getChildren(response.type, parseInt(response.id, 10))
          );

          let columns = Object.assign([], this.state.columns);
          if (clearParentalColumns) {
            clearParentalColumns = false;

            columns = columns.slice(columnIndex);
            columns.unshift([]);

            columns[1] = columns[1].filter((element) => {
              if (followUpType === element.props.type && followUpId === element.props.id) {
                return element;
              }
              return null;
            });
          }
          columns[0].push(resolvedElement);

          this.setState({ columns });
        });
      })
      .catch(() => this.setState({ hasError: true }));
  }

  getChildren(followUpType, followUpId) {
    const columnIndex = this.getElementColumnIndex(followUpType, followUpId);
    let clearChildColumns = true;

    fetchFollowUpElementChildren(followUpType, followUpId)
      .then((multipleResponse) => {
        multipleResponse.forEach((response) => {
          const resolvedElement = resolveElement(
            response,
            () => this.getParents(response.type, parseInt(response.id, 10)),
            () => this.getChildren(response.type, parseInt(response.id, 10))
          );

          let columns = Object.assign([], this.state.columns);
          if (clearChildColumns) {
            clearChildColumns = false;

            columns = columns.slice(0, columnIndex + 1);
            columns.push([]);

            columns[columnIndex] = columns[columnIndex].filter((element) => {
              if (followUpType === element.props.type && followUpId === element.props.id) {
                return element;
              }
              return null;
            });
          }
          columns[columnIndex + 1].push(resolvedElement);

          this.setState({ columns });
        });
      })
      .catch(() => this.setState({ hasError: true }));
  }

  render() {
    if (this.state.hasError) {
      return (
        <p className="alert-error text-center">
          Error occurred. Followup timeline cannot be loaded.
        </p>
      );
    }

    return (
      <FollowUpTimeline
        infoLink={`/followup/index/kid/${this.props.consultationId}`}
        infoLinkTitle="Zurück zur Übersicht Reaktionen & Wirkungen"
        infoText={
          'Verfolge hier, welche Reaktionen es auf den Beitrag gab. ' +
          'Klicke auf die Pfeile für nächste Schritte.'
        }
        columns={this.state.columns}
      />
    );
  }
}

FollowUpContainer.propTypes = {
  consultationId: React.PropTypes.number.isRequired,
  followUpType: React.PropTypes.oneOf(['contribution', 'snippet']).isRequired,
  followUpId: React.PropTypes.number.isRequired,
};

export default FollowUpContainer;
