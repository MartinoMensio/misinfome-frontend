import { Component } from '@angular/core';
import { ApiService, LoadStates } from '../api.service';
import { ActivatedRoute, Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-tweet-result',
  templateUrl: './tweet-result.component.html',
  styleUrls: ['./tweet-result.component.css']
})
export class TweetResultComponent {
  loadStates = LoadStates;

  result: any;
  error_detail = '';
  main_loading_string = '';
  main_load_state = LoadStates.None;

  constructor(private apiService: ApiService, private router: Router, private route: ActivatedRoute, private modalService: NgbModal) {
    this.main_load_state = LoadStates.Loading;
    this.main_loading_string = 'Analysing tweet...';

    this.apiService.getTweet(this.route.snapshot.params['tweet_id']).subscribe((data: any) => {
      console.log(data);
      this.result = data;
      this.result.visible = true;
      this.main_load_state = LoadStates.Loaded;
    }, (error) => {
      console.log(error);
      this.main_load_state = LoadStates.Error;
    });
  }

  toggleAnalysis(match: any) {
    if (match.visible) {
      match.visible = false;
    } else {
      match.visible = true;
    }
  }

}
