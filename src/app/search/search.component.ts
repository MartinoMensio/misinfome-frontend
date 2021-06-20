import { Component, Input, OnInit } from '@angular/core';
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

  constructor(private apiService: ApiService) { }

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
  }

}
