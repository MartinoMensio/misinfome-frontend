import { Component, OnDestroy, OnInit } from '@angular/core';
import { FormControl, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Subscription } from 'rxjs';
import { first, map } from 'rxjs/operators';
import { AnalysisModalComponent } from '../analysis-modal/analysis-modal.component';
import { ApiService, LoadStates } from '../api.service';

@Component({
  selector: 'app-profiles-results',
  templateUrl: './profiles-results.component.html',
  styleUrls: ['./profiles-results.component.css']
})
export class ProfilesResultsComponent implements OnInit, OnDestroy {
  state_username: string = '';
  until_id: number | null = null;

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

  

  private sub: any;

  constructor(private apiService: ApiService, private router: Router, private route: ActivatedRoute, private modalService: NgbModal) { }

  ngOnInit() {
    this.sub = this.route.params.subscribe(params => {
      this.state_username = params['username'];
      // this.username.setValue(params['username']);
      // console.log('sub called ' + this.username.value);
      if (this.state_username) {
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
    console.log(`processing until_id = ${this.until_id}`);
    if (!this.has_results) {
      // first time
      this.main_load_state = LoadStates.Loading;
    } else {
      // further results
      this.next_results_state = LoadStates.Loading
    }

    this.apiService.analyseProfile(this.state_username, this.until_id).pipe(
      map(result_update => {
        console.log('map step')
        console.log(result_update);
        // update the message
        const result_update_state = result_update.state.replace('PENDING', '...')
        if (!this.has_results) {
          this.main_loading_string = result_update_state;
        } else {
          this.next_results_loading_string = result_update_state;
        }
        return result_update;
      }),
      // wait until success
      first((res: any) => res.state === 'SUCCESS'),
      // then unwrap the result
      map((result_ok: any) => result_ok.result)
    ).subscribe((result: any) => {
      // SUCCESS
      console.log(result)
      const next_until_id = result.next_until_id;
      this.results = result;
      // update interface
      if (!this.has_results) {
        // first time
        this.main_load_state = LoadStates.Loaded;
      } else {
        // already some results
        this.next_results_state = LoadStates.Loaded;
      }
      this.has_results = true;
      if (next_until_id) {
        this.until_id = next_until_id;
        // show the continue analysis button
        this.continue_analysis_enabled = true;
        // and get another group of tweets if enabled
        if (this.continue_analysis || this.results.tweets_analysed_stats.tweets_retrieved_count <=400) {
          this.analyse();
        }
      } else {
        this.analysis_finished = true;
        // hide button
        this.continue_analysis_enabled = false;
      }
      // flag for next time
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
}
