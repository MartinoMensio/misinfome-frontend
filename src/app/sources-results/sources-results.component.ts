import { Component, OnDestroy, OnInit } from '@angular/core';
import { ApiService, LoadStates } from '../api.service';
import { ActivatedRoute, Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { AnalysisModalComponent } from '../analysis-modal/analysis-modal.component';
import { first, map } from 'rxjs';

@Component({
  selector: 'app-sources-results',
  templateUrl: './sources-results.component.html',
  styleUrls: ['./sources-results.component.css']
})
export class SourcesResultsComponent implements OnInit, OnDestroy {
  state_source: string = '';

  loadStates = LoadStates;
  main_load_state = LoadStates.None;
  main_loading_string = '';
  has_results: boolean = false;
  analysis_finished = false;
  next_results_state = LoadStates.None;
  next_results_loading_string = '';
  error_detail = '';
  continue_analysis = false;
  continue_analysis_enabled = false;

  results: any;

  // pagination
  p: number = 1;

  tweets_sorted_by: string = 'credibility';

  sourceAssessment: any;
  chartData: any;
  factcheckingReports: any;



  private sub: any;

  constructor(private apiService: ApiService, private router: Router, private route: ActivatedRoute, private modalService: NgbModal) { }

  ngOnInit() {
    this.sub = this.route.params.subscribe(params => {
      this.state_source = params['source_id'];
      // this.username.setValue(params['username']);
      // console.log('sub called ' + this.username.value);
      if (this.state_source) {
        this.continue_analysis = false;
        this.continue_analysis_enabled = false;
        this.results = false;
        this.has_results = false;
        this.analysis_finished = false;
        this.analyse();
        this.p = 1;
      }
    });
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
  }

  continueAnalysis() {
    if (this.continue_analysis_enabled) {
      this.continue_analysis = true;
      if (this.next_results_state !== LoadStates.Loading) {
        this.analyse();
      }
    }
  }

  pageChanged(page: any) {
    this.p = page;
  }

  pageBoundsCorrection(page: any) {
    this.p = page;
  }

  toggleAnalysis(match: any) {
    if (match.visible) {
      match.visible = false;
    } else {
      match.visible = true;
    }
  }

  open_analysis_modal() {
    const modalRef = this.modalService.open(AnalysisModalComponent);
  }

  analyse() {
    // processing
    if (!this.has_results) {
      // first time
      this.main_load_state = LoadStates.Loading;
    } else {
      // further results
      this.next_results_state = LoadStates.Loading
    }

    this.apiService.analyseSource(this.state_source).subscribe((result: any) => {
      // SUCCESS
      console.log(result)
      this.results = result;
      // update interface
      this.main_load_state = LoadStates.Loaded;
      this.has_results = true;
      this.analysis_finished = true;
      // convert
      this.getSourceAssessmentsByType(this.results);
    }, (error) => {
      // ERROR
      console.log('error');
      console.log(error);
      if (!this.has_results) {
        // first time
        this.main_load_state = LoadStates.Error;
      } else {
        // already some results
        this.next_results_state = LoadStates.Error;
      }
      this.error_detail = error.error.detail;
    })
  }

  sort_changed(sort: any) {
    // this.tweets_sorted_by = sort;
    console.log(this.tweets_sorted_by);
    this.results.matching_tweets = this.results.matching_tweets.sort((a: any, b: any) => {
      return parseInt(a.tweet.id) < parseInt(b.tweet.id) ? 1 : -1;
    });
  }

  getSourceAssessmentsByType(sourceAssessments: any) {
    sourceAssessments.assessments_without_fc = [];
    let factcheckingReports;
    sourceAssessments.assessments.forEach((element: any, index: number) => {
      if (element.origin_id === 'factchecking_report') {
        factcheckingReports = element;
        let reports = element.reports;
        // sourceAssessments.assessments.splice(index, 1);
        let by_factchecker = reports.reduce((prev: any, curr: any) => {
          if (!curr.origin) {
            // Not IFCN
            return prev;
          }
          const group = curr.origin.id;
          const coinform_label = curr.coinform_label;
          if (!prev[group]) {
            prev[group] = {}
            // prev.origin_id = group
          }
          if (!prev[group][coinform_label]) {
            prev[group][coinform_label] = new Set()
          }
          prev[group][coinform_label].add(curr.report_url)
          return prev;
        }, {});
        console.log(by_factchecker);
        let chartData = [];
        for (let origin_id in by_factchecker) {
          const by_origin = by_factchecker[origin_id];
          // const series = []
          // for (let label in by_origin) {
          //   const count = by_origin[label].size;
          //   // series.push({
          //   //   name: label,
          //   //   value: count
          //   // })
          // }
          // TODO at the moment this must be done in strict order for the colours to work
          const series = [{
            name: 'credible',
            value: (by_origin['credible'] || new Set()).size
          }, {
            name: 'mostly credible',
            value: (by_origin['mostly_credible'] || new Set()).size
          }, {
            name: 'uncertain',
            value: (by_origin['uncertain'] || new Set()).size
          }, {
            name: 'not credible',
            value: (by_origin['not_credible'] || new Set()).size
          }, {
            name: 'not verifiable',
            value: (by_origin['not_verifiable'] || new Set()).size
          },
          ]
          chartData.push({
            name: origin_id,
            series: series
          })
        }

        // this.chartData = by_factchecker.reduce((prev: Array<any>, curr) => {
        //   prev.push({
        //     name: curr.origin_id || 'overall',
        //     series: [{
        //       name: 'not_credible',
        //       value: (curr.not_credible || new Set()).size,
        //     }, //{
        //     //   name: 'negative',

        //     //   value: (curr.negative || new Set()).size
        //     // }, {
        //     //   name: 'neutral',
        //     //   value: (curr.neutral || []).length,

        //     // }, {
        //     //   name: 'unknown',
        //     //   value: (curr.unknown || []).length

        //     // }
        //   ]
        //   });
        //   return prev;
        // }, [])
        console.log(chartData)
      } else {
        sourceAssessments.assessments_without_fc.push(element);
      }
    });
    this.sourceAssessment = sourceAssessments;
    this.factcheckingReports = factcheckingReports;
    console.log('factcheckingReports');
    console.log(this.factcheckingReports);
    // this.chartData = chartData;
  }

}
