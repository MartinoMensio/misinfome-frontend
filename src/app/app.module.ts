import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './home/home.component';
import { SearchComponent } from './search/search.component';
import { ProfilesResultsComponent } from './profiles-results/profiles-results.component';
import { HttpClientModule } from '@angular/common/http';
import { CountUpModule } from 'ngx-countup';
import { NgxPaginationModule } from 'ngx-pagination';
import { AboutComponent } from './about/about.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ContactModalComponent } from './contact-modal/contact-modal.component';
import { AnalysisModalComponent } from './analysis-modal/analysis-modal.component';
import { AccuracyModalComponent } from './accuracy-modal/accuracy-modal.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { TweetResultComponent } from './tweet-result/tweet-result.component';
import { SourcesResultsComponent } from './sources-results/sources-results.component';
import { SearchSourceComponent } from './search-source/search-source.component';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    SearchComponent,
    ProfilesResultsComponent,
    AboutComponent,
    ContactModalComponent,
    AnalysisModalComponent,
    AccuracyModalComponent,
    TweetResultComponent,
    SourcesResultsComponent,
    SearchSourceComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    CountUpModule,
    NgxPaginationModule,
    NgbModule,
    ReactiveFormsModule,
    FormsModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
