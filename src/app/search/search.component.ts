import { Component, Input, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from '../api.service';

declare var $: any;

@Component({
  selector: 'app-search',
  templateUrl: './search.component.html',
  styleUrls: ['./search.component.css']
})
export class SearchComponent implements OnInit {

  @Input() expanded: boolean = false;
  most_popular_entries = [];
  counter = 0;
  state_username: string = '';

  
  searchForm = new FormGroup({
    username: new FormControl('', [Validators.required, Validators.pattern('[a-zA-Z0-9_]+')])
  })
  private sub: any;

  constructor(private apiService: ApiService, private route: ActivatedRoute, private router: Router) { }

  ngOnInit(): void {
    // Animate Search Suggestions
    // var terms = $("ul.animate-text li");
    this.apiService.getMostPopularEntries().subscribe((result: any) => {
      this.most_popular_entries = result;
      console.log(this.most_popular_entries);
    })

    // const rotateTerm = () => {
    //     // var ct = $("#rotate").data("term") || 0;
    //     // console.log(terms.eq([ct]).text());
    //     $("#rotate").data("term", ct == this.most_popular_entries.length - 1 ? 0 : ct + 1).text(terms.eq([ct]).text()).fadeIn(800).delay(5000).fadeOut(800, rotateTerm);
    // }
    // $(rotateTerm);

    this.sub = this.route.params.subscribe(params => {
      this.state_username = params['username'];
      this.searchForm.patchValue({username: params['username']});
      console.log('sub called ' + this.searchForm.value.username);
      // if (this.username.value) {
      //   this.continue_analysis = false;
      //   this.continue_analysis_enabled = false;
      //   this.analyse();
      // }
    });

  }

  onSubmit() {
    console.log('submit with ' + this.searchForm.value.username, ' from ' + this.state_username);
    if (this.state_username !== this.searchForm.value.username) {
      return this.router.navigate(['profiles', this.searchForm.value.username]);
    } else {
      // reload current page
      // return this.ngOnInit();
      return;
    }
  }

}
