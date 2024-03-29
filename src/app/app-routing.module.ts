import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AboutComponent } from './about/about.component';
import { HomeComponent } from './home/home.component';
import { ProfilesResultsComponent } from './profiles-results/profiles-results.component';
import { TweetResultComponent } from './tweet-result/tweet-result.component';
import { SourcesResultsComponent } from './sources-results/sources-results.component';

const routes: Routes = [
  { path: 'home', component: HomeComponent },
  { path: 'about', component: AboutComponent },
  { path: 'profiles/:username', component: ProfilesResultsComponent },
  { path: 'tweets/:tweet_id', component: TweetResultComponent },
  { path: 'sources/:source_id', component: SourcesResultsComponent },
  { path: 'sources', component: SourcesResultsComponent },
  { path: '', redirectTo: '/home', pathMatch: 'full' },
];

@NgModule({
  imports: [RouterModule.forRoot(routes, { scrollPositionRestoration: 'enabled' })],
  exports: [RouterModule]
})
export class AppRoutingModule { }
